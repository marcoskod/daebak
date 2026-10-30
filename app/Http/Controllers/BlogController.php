<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\ProductDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();
        $q = trim($request->string('q')->toString());
        $filtered = $category !== '' || $q !== '';

        $query = BlogPost::published()->orderByDesc('published_at');
        if (in_array($category, BlogPost::CATEGORIES, true)) {
            $query->where('category', $category);
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('translations', 'like', "%{$q}%")->orWhere('tags', 'like', "%{$q}%"));
        }

        $featured = null;
        if (! $filtered && (int) $request->query('page', 1) === 1) {
            $featured = BlogPost::published()->where('is_featured', true)->orderByDesc('published_at')->first();
            if ($featured) {
                $query->where('id', '!=', $featured->id);
            }
        }

        $counts = BlogPost::published()->selectRaw('category, count(*) as n')->groupBy('category')->pluck('n', 'category');

        return view('blog.index', [
            'posts' => $query->paginate(9)->withQueryString(),
            'featured' => $featured,
            'category' => $category,
            'q' => $q,
            'counts' => $counts,
            'total' => $counts->sum(),
        ]);
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->is_published && (! $post->published_at || $post->published_at->isPast()), 404);
        $post->increment('views');

        $parsed = ProductDescription::parse($post->content);
        // Ancres stables (les titres coréens ne donnent pas de slug ASCII).
        foreach ($parsed['sections'] as $i => &$s) {
            $s['anchor'] = 's'.($i + 1).'-'.Str::limit(Str::slug($s['title']), 30, '');
        }
        unset($s);

        $published = BlogPost::published();

        return view('blog.show', [
            'post' => $post,
            'parsed' => $parsed,
            'products' => $post->relatedProducts(),
            'related' => (clone $published)->where('id', '!=', $post->id)->where('category', $post->category)->orderByDesc('published_at')->take(3)->get(),
            'prev' => (clone $published)->where('published_at', '<', $post->published_at)->orderByDesc('published_at')->first(),
            'next' => (clone $published)->where('published_at', '>', $post->published_at)->orderBy('published_at')->first(),
        ]);
    }
}
