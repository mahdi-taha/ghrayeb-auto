<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\SitemapController;
use App\Models\GalleryItem;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $services = Service::query()
        ->active()
        ->inHomepageOrder()
        ->limit(6)
        ->get();
    $productCategories = ProductCategory::query()
        ->active()
        ->inHomepageOrder()
        ->limit(6)
        ->get();
    $galleryItems = GalleryItem::query()
        ->active()
        ->inHomepageOrder()
        ->limit(8)
        ->get();

    return view('home', compact('services', 'productCategories', 'galleryItems'));
});

Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('products.show');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', fn () => response()
    ->view('robots')
    ->header('Content-Type', 'text/plain; charset=UTF-8'))
    ->name('robots');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::fallback(fn () => response()->view('errors.404', status: 404));
