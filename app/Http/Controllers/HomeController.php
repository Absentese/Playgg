<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('available', true)
            ->where('is_featured', true)
            ->with('category')
            ->limit(8)
            ->get();
        $saleProducts = Product::where('available', true)
            ->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price')
            ->with('category')
            ->orderByRaw('(old_price - price) DESC')
            ->limit(8)
            ->get();

        return view('shop.index', compact('featuredProducts', 'saleProducts'));
    }

    public function about()
    {
        return view('shop.about');
    }

    public function privacy()
    {
        return view('shop.legal.privacy');
    }

    public function offer()
    {
        return view('shop.legal.offer');
    }
}
