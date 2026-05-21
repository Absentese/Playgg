<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('paid')) {
            $query->where('paid', $request->boolean('paid'));
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.category']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUSES)),
            'paid' => 'required|boolean',
        ]);

        $order->update($data);

        return back()->with('success', 'Заказ обновлён.');
    }

    public function destroy(Order $order)
    {
        $label = $order->numberLabel();
        $order->items()->delete();
        $order->delete();
        $this->syncOrderIdSequence();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Заказ {$label} удалён.");
    }

    private function syncOrderIdSequence(): void
    {
        if (config('database.default') !== 'sqlite') {
            return;
        }

        $maxId = Order::max('id') ?? 0;

        if ($maxId === 0) {
            DB::statement("DELETE FROM sqlite_sequence WHERE name = 'orders'");
        } else {
            DB::statement('UPDATE sqlite_sequence SET seq = ? WHERE name = ?', [$maxId, 'orders']);
        }
    }
}
