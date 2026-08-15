<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();
        $categories = ProductCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $products = Product::query()
            ->with('category')
            ->publiclyVisible()
            ->when($categorySlug, fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug),
            ))
            ->inCatalogOrder()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('categories', 'products', 'categorySlug'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        abort_unless($product->category?->is_active, 404);

        $relatedProducts = Product::query()
            ->with('category')
            ->publiclyVisible()
            ->where('product_category_id', $product->product_category_id)
            ->whereKeyNot($product->getKey())
            ->inCatalogOrder()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
