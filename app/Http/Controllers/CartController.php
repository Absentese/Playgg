<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Profile;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(private PromoCodeService $promoCodes) {}

    public function index()
    {
        $cart = $this->getCart()->load('items.product.category');
        $this->normalizeQuantities($cart);
        $summary = $this->promoCodes->summaryForCart($cart, auth()->user());

        $initial = Profile::checkoutDefaultsFor(auth()->user()->load('profile'));

        return view('shop.cart', compact('cart', 'summary', 'initial'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = $this->getCart();
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            return redirect()
                ->route('product.show', $product)
                ->with('info', "«{$product->name}» уже в корзине. Цифровой ключ — один экземпляр на аккаунт.");
        }

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        return redirect()
            ->route('product.show', $product)
            ->with('success', "Игра «{$product->name}» добавлена в корзину");
    }

    public function remove(CartItem $item)
    {
        abort_unless($item->cart->user_id === auth()->id(), 403);
        $item->delete();

        return redirect()->route('cart')->with('success', 'Игра удалена из корзины');
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        $cart = $this->getCart()->load('items.product');
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Корзина пуста');
        }

        try {
            $promo = $this->promoCodes->apply(
                $request->input('promo_code'),
                auth()->user(),
                $cart->totalPrice()
            );

            return back()->with('success', "Промокод «{$promo->code}» применён");
        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    public function removePromo()
    {
        $this->promoCodes->clear();

        return back()->with('success', 'Промокод удалён');
    }

    private function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    private function normalizeQuantities(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            if ($item->quantity !== 1) {
                $item->update(['quantity' => 1]);
            }
        }
    }
}
