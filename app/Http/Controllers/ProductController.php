<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
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
            $products->search($searchQuery);
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

    public function search(Request $request): JsonResponse
    {
        $searchQuery = trim((string) $request->query('q', ''));

        if (mb_strlen($searchQuery) < 1) {
            return response()->json(['items' => []]);
        }

        $lower = mb_strtolower($searchQuery, 'UTF-8');
        $namePrefix = $lower.'%';
        $contains = '%'.$lower.'%';

        $products = Product::query()
            ->where('available', true)
            ->search($searchQuery)
            ->orderByRaw(
                'CASE WHEN LOWER(name) LIKE ? THEN 0 WHEN LOWER(slug) LIKE ? THEN 1 ELSE 2 END',
                [$namePrefix, $contains],
            )
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json([
            'items' => $products->map(fn (Product $product) => [
                'name' => $product->name,
                'url' => route('product.show', $product),
                'image' => $product->imageUrl(),
                'platform' => $product->platform,
                'price' => (float) $product->price,
                'old_price' => $product->old_price ? (float) $product->old_price : null,
                'discount_percent' => $product->discountPercent(),
            ]),
            'catalog_url' => route('products', self::catalogParams($request, ['q' => $searchQuery])),
        ]);
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
