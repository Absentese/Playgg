<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Profile;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private PromoCodeService $promoCodes) {}

    public function index()
    {
        $orders = auth()->user()->orders()->latest()->get();

        return view('shop.orders', compact('orders'));
    }

    public function create()
    {
        $cart = $this->getCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Корзина пуста');
        }

        return redirect()->route('cart')->with('info', 'Оформление и оплата — в корзине.');
    }

    public function store(Request $request)
    {
        $user = auth()->user()->load('profile');

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email',
                'steam_profile_url' => 'nullable|string|max:255|url',
                'promo_code' => 'nullable|string|max:50',
                'payment_method' => 'required|in:card,sbp',
                'card_number' => 'required_if:payment_method,card|nullable|string|max:24',
                'card_name' => 'required_if:payment_method,card|nullable|string|max:100',
                'card_expiry' => 'required_if:payment_method,card|nullable|string|max:7',
                'card_cvv' => 'required_if:payment_method,card|nullable|string|max:4',
            ]);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('cart'));
        }

        $validated['steam_id'] = $user->profile?->steam_id ?? '';
        $validated['steam_profile_url'] = $validated['steam_profile_url'] ?? $user->profile?->steam_profile_url;
        $data = $validated;

        $cart = $this->getCart()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart');
        }

        $subtotal = $cart->totalPrice();
        $promo = null;
        $discount = 0.0;

        if ($request->filled('promo_code')) {
            try {
                $promo = $this->promoCodes->resolve($request->input('promo_code'), auth()->user(), $subtotal);
                $discount = $this->promoCodes->calculateDiscount($promo, $subtotal);
            } catch (ValidationException $e) {
                return redirect()->route('cart')->withInput()->withErrors($e->errors());
            }
        } else {
            $promo = $this->promoCodes->getApplied(auth()->user(), $subtotal);
            if ($promo) {
                $discount = $this->promoCodes->calculateDiscount($promo, $subtotal);
            }
        }

        $total = max(0, round($subtotal - $discount, 2));

        $order = DB::transaction(function () use ($data, $cart, $subtotal, $discount, $total, $promo) {
            $order = Order::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'steam_id' => $data['steam_id'],
                'steam_profile_url' => $data['steam_profile_url'],
                'user_id' => auth()->id(),
                'status' => 'processing',
                'paid' => true,
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total' => $total,
                'promo_code_id' => $promo?->id,
                'promo_code' => $promo?->code,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            if ($promo) {
                $this->promoCodes->markUsed($promo);
            }

            $cart->items()->delete();
            $this->promoCodes->clear();

            return $order;
        });

        $profile = $user->profile ?? $user->profile()->create([]);
        $profile->syncCheckoutData($data);

        $paidMessage = $data['payment_method'] === 'sbp'
            ? 'Оплата через СБП прошла успешно! Ключ будет выдан на Steam в течение 5–15 минут.'
            : 'Оплата прошла успешно! Ключ будет выдан на Steam в течение 5–15 минут.';

        return redirect()
            ->route('order.show', $order)
            ->with('success', $paidMessage);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product.category');

        return view('shop.order.detail', compact('order'));
    }

    public function payment(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_if($order->paid, 403);

        return view('shop.order.payment', compact('order'));
    }

    public function processPayment(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $request->validate([
            'payment_method' => 'required|in:card,sbp',
            'card_number' => 'required_if:payment_method,card|nullable|string|max:24',
            'card_name' => 'required_if:payment_method,card|nullable|string|max:100',
            'card_expiry' => 'required_if:payment_method,card|nullable|string|max:7',
            'card_cvv' => 'required_if:payment_method,card|nullable|string|max:4',
        ]);

        $order->update([
            'paid' => true,
            'status' => 'processing',
            'payment_method' => $request->input('payment_method'),
        ]);

        return redirect()
            ->route('order.show', $order)
            ->with('success', 'Оплата прошла успешно! Заказ передан в обработку.');
    }

    private function getCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }
}
