<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductImageService $images,
    ) {}
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('available')) {
            $query->where('available', $request->boolean('available'));
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:5000',
            'available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);

        $data = $this->normalizePrices($data);

        $slug = Str::slug($data['name']);
        $baseSlug = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        unset($data['image']);

        $product = Product::create([
            ...$data,
            'slug' => $slug,
        ]);

        if ($request->hasFile('image')) {
            $this->images->store($product, $request->file('image'));
        }

        return back()->with('success', 'Игра добавлена.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'available' => 'required|boolean',
        ]);

        $product->update($this->normalizePrices($data));

        return back()->with('success', 'Игра обновлена.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Нельзя удалить игру: она присутствует в заказах.');
        }

        $this->images->deleteFile($product);
        $product->delete();

        return back()->with('success', 'Игра удалена.');
    }

    public function storeImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);

        $this->images->store($product, $request->file('image'));

        return back()->with('success', 'Фото игры обновлено.');
    }

    public function destroyImage(Product $product)
    {
        $this->images->delete($product);

        return back()->with('success', 'Фото игры удалено.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePrices(array $data): array
    {
        if (empty($data['old_price']) || (float) $data['old_price'] <= (float) $data['price']) {
            $data['old_price'] = null;
        }

        return $data;
    }
}
