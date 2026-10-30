<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function home(): View
    {
        $active = fn () => Product::query()->with('category')->where('is_active', true);

        return view('shop.home', [
            'bestsellers' => $active()->where('badge', 'bestseller')->orderByDesc('reviews_count')->take(8)->get(),
            'newest' => $active()->orderByDesc('id')->take(8)->get(),
            'sale' => $active()->whereNotNull('sale_price')->take(4)->get(),
            'categories' => $this->parentCategories(),
            'reviews' => ProductReview::query()->with('product')->where('rating', 5)->latest()->take(18)->get()->groupBy('locale')->flatMap(fn ($g) => $g->take(2))->take(6)->values(),
            'collections' => site('collections'),
            'posts' => \App\Models\BlogPost::published()->orderByDesc('published_at')->take(3)->get(),
            'stats' => ['products' => Product::where('is_active', true)->count(), 'reviews' => ProductReview::count()],
        ]);
    }

    public function catalogue(Request $request): View
    {
        return view('shop.catalogue', $this->catalogueData($request));
    }

    public function collections(): View
    {
        return view('shop.collections', ['collections' => site('collections'), 'categories' => $this->parentCategories()]);
    }

    public function collection(Request $request, string $key): View
    {
        abort_unless(isset(config('site.collections')[$key]), 404);
        $request->merge(['collection' => $key]);

        return view('shop.catalogue', $this->catalogueData($request) + ['collectionKey' => $key]);
    }

    protected function parentCategories()
    {
        return Category::query()
            ->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->get()
            ->each(function (Category $c) {
                $ids = $c->children->pluck('id')->push($c->id);
                $c->products_total = Product::where('is_active', true)->whereIn('category_id', $ids)->count();
            })
            ->filter(fn (Category $c) => $c->products_total > 0)
            ->values();
    }

    protected function catalogueData(Request $request): array
    {
        $products = $this->filteredProducts($request)->paginate(12)->withQueryString();
        $activeSlug = $request->string('category')->toString();
        $activeCategory = $activeSlug !== '' ? Category::where('slug', $activeSlug)->first() : null;

        return [
            'categories' => $this->parentCategories(),
            'products' => $products,
            'activeSlug' => $activeSlug,
            'activeParentSlug' => $activeCategory ? ($activeCategory->parent?->slug ?? $activeCategory->slug) : null,
            'activeCategory' => $activeCategory,
            'types' => Product::where('is_active', true)->distinct()->pluck('product_type')->filter()->values(),
            'collectionKey' => $request->string('collection')->toString() ?: null,
            'sort' => $request->string('sort')->toString() ?: 'popular',
        ];
    }

    public function catalogueFilter(Request $request): JsonResponse
    {
        $products = $this->filteredProducts($request)->paginate(12)->withQueryString();

        return response()->json([
            'html' => view('shop.partials.product-grid', compact('products'))->render(),
            'pagination' => (string) $products->links(),
            'total' => $products->total(),
        ]);
    }

    protected function filteredProducts(Request $request)
    {
        $query = Product::query()->with('category')->where('is_active', true);

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhere('translations', 'like', $term)
                ->orWhere('keywords', 'like', $term));
        }

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug)
                ->orWhereIn('parent_id', Category::where('slug', $slug)->select('id')));
        }

        if ($request->filled('collection') && ($tags = config('site.collections.'.$request->string('collection'))['tags'] ?? null)) {
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if ($request->filled('type')) {
            $query->where('product_type', $request->string('type'));
        }

        if ($request->boolean('sale')) {
            $query->whereNotNull('sale_price');
        }

        return match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'newest' => $query->latest('id'),
            'rating' => $query->orderByDesc('rating')->orderByDesc('reviews_count'),
            default => $query->orderByDesc('reviews_count'),
        };
    }

    public function produit(Request $request, Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load('category.parent');

        $reviews = $product->reviews()->latest()->get();
        $histogram = collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($n) => [$n => $reviews->where('rating', $n)->count()]);

        $related = Product::query()->with('category')->where('is_active', true)->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id);
                foreach ($product->tags ?? [] as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            })
            ->orderByDesc('reviews_count')->take(8)->get();

        return view('shop.produit', [
            'product' => $product,
            'reviews' => $reviews,
            'histogram' => $histogram,
            'related' => $related,
            'template' => $product->descriptionTemplate($request->query('tpl')),
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $q = trim($request->string('q')->toString());
        abort_if(mb_strlen($q) < 2, 422);

        $items = Product::query()->with('category')->where('is_active', true)
            ->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('translations', 'like', "%{$q}%")->orWhere('keywords', 'like', "%{$q}%"))
            ->orderByDesc('reviews_count')->take(6)->get()
            ->map(fn (Product $p) => [
                'name' => $p->localized_name,
                'url' => route('shop.produit', $p->slug),
                'image' => $p->image_url,
                'category' => $p->category?->localized_name,
                'price' => money($p->current_price),
            ]);

        return response()->json(['items' => $items]);
    }

    public function wishlist(): View
    {
        return view('shop.wishlist');
    }

    public function wishlistItems(Request $request): JsonResponse
    {
        $ids = collect(explode(',', (string) $request->query('ids')))->map(fn ($i) => (int) $i)->filter()->take(60);
        $products = Product::query()->with('category')->where('is_active', true)->whereIn('id', $ids)->get();

        return response()->json(['html' => $products->isEmpty() ? '' : view('shop.partials.product-grid', compact('products'))->render()]);
    }
}
