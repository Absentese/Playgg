<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category', 'all');
        $sort = $request->query('sort', 'new');
        $searchQuery = trim((string) $request->query('q', ''));
        $products = Product::query()->where('available', true)->with('category');

        if ($searchQuery !== '') {
            $products->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        if ($categorySlug !== 'all') {
            $products->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($request->boolean('sale')) {
            $products->whereNotNull('old_price')->whereColumn('old_price', '>', 'price');
        }

        if ($request->boolean('preorder')) {
            $products->where('is_preorder', true);
        }

        match ($sort) {
            'price_asc' => $products->orderBy('price'),
            'price_desc' => $products->orderByDesc('price'),
            'discount' => $products->orderByRaw('(old_price - price) DESC'),
            default => $products->latest(),
        };

        return view('shop.products', [
            'products' => $products->get(),
            'categories' => Category::orderBy('name')->get(),
            'currentCategory' => $categorySlug,
            'currentSort' => $sort,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function catalogParams(Request $request, array $overrides = []): array
    {
        $params = [];

        if ($request->filled('q')) {
            $params['q'] = trim((string) $request->get('q'));
        }

        if ($request->get('sort', 'new') !== 'new') {
            $params['sort'] = $request->get('sort');
        }

        if ($request->get('category', 'all') !== 'all') {
            $params['category'] = $request->get('category');
        }

        if ($request->boolean('sale')) {
            $params['sale'] = '1';
        }

        if ($request->boolean('preorder')) {
            $params['preorder'] = '1';
        }

        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function show(Product $product)
    {
        abort_unless($product->available, 404);

        $relatedProducts = Product::query()
            ->where('category_id', $product->category_id)
            ->where('available', true)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('shop.product_detail', compact('product', 'relatedProducts'));
    }
}
