<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

// Préférences (langue, devise, newsletter)
Route::get('/lang/{locale}', [PreferencesController::class, 'locale'])->name('lang.switch');
Route::get('/currency/{code}', [PreferencesController::class, 'currency'])->name('currency.switch');
Route::post('/newsletter', [PreferencesController::class, 'newsletter'])->middleware('throttle:contact')->name('newsletter.store');

// Boutique
Route::get('/', [ShopController::class, 'home'])->name('shop.home');
Route::get('/shop', [ShopController::class, 'catalogue'])->name('shop.catalogue');
Route::get('/shop/filter', [ShopController::class, 'catalogueFilter'])->name('shop.catalogue.filter');
Route::get('/shop/suggest', [ShopController::class, 'suggest'])->name('shop.suggest');
Route::get('/collections', [ShopController::class, 'collections'])->name('shop.collections');
Route::get('/collections/{key}', [ShopController::class, 'collection'])->name('shop.collection');
Route::get('/product/{product:slug}', [ShopController::class, 'produit'])->name('shop.produit');
Route::get('/wishlist', [ShopController::class, 'wishlist'])->name('shop.wishlist');
Route::get('/wishlist/items', [ShopController::class, 'wishlistItems'])->name('shop.wishlist.items');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Panier & commande
Route::get('/cart', [CartController::class, 'index'])->name('shop.panier');
Route::post('/cart/add', [CartController::class, 'add'])->middleware('throttle:cart')->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->middleware('throttle:cart')->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->middleware('throttle:cart')->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware(['throttle:checkout', 'early'])->name('checkout.store');
Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/checkout/demo/{order}', [CheckoutController::class, 'demo'])->name('checkout.demo');
Route::post('/checkout/demo/{order}/pay', [CheckoutController::class, 'demoPay'])->name('checkout.demo.pay');

Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('tracking.index');
Route::post('/track-order', [OrderTrackingController::class, 'find'])->middleware('throttle:tracking')->name('tracking.find');

// Pages d'information
Route::get('/how-it-works', [StaticPageController::class, 'how'])->name('pages.how');
Route::get('/studio', [StaticPageController::class, 'studio'])->name('pages.studio');
Route::get('/creators', [StaticPageController::class, 'creators'])->name('pages.creators');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('pages.faq');
Route::get('/shipping-returns', [StaticPageController::class, 'shipping'])->name('pages.shipping');
Route::get('/size-guide', [StaticPageController::class, 'sizeGuide'])->name('pages.size-guide');
Route::get('/terms', [StaticPageController::class, 'cgv'])->name('pages.cgv');
Route::get('/privacy', [StaticPageController::class, 'confidentialite'])->name('pages.confidentialite');
Route::get('/legal-notice', [StaticPageController::class, 'mentionsLegales'])->name('pages.mentions-legales');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [StaticPageController::class, 'contactStore'])->middleware(['throttle:contact', 'early'])->name('pages.contact.store');

// Chat (assistant IA + mode manuel)
Route::prefix('chat')->middleware('throttle:chat')->group(function () {
    Route::post('/message', [ChatController::class, 'message'])->name('chat.message');
    Route::get('/history', [ChatController::class, 'history'])->name('chat.history');
    Route::post('/handoff', [ChatController::class, 'handoff'])->name('chat.handoff');
    Route::get('/poll', [ChatController::class, 'poll'])->name('chat.poll');
});

// Compte
Route::get('/dashboard', fn () => view('dashboard', ['orders' => auth()->user()->orders()->latest()->limit(8)->get()]))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Cible des ErrorDocument du serveur web : réaffiche la page d'erreur personnalisée.
Route::get('/erreur/{code}', fn (int $code) => abort($code))->whereIn('code', [400, 401, 403, 404, 419, 429, 500, 503]);

require __DIR__.'/auth.php';
