<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $products = Product::query()
            ->publiclyVisible()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->get();

        return response()
            ->view('sitemap', compact('products'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
