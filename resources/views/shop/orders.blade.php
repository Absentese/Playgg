@extends('shop.layouts.app')
@section('title', 'Мои заказы')
@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header text-white py-3" style="background: var(--primary);">
            <h2 class="mb-0 h4"><i class="fas fa-list me-2"></i>Мои заказы</h2>
        </div>
        <div class="card-body p-0">
            @if($orders->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover mb-0 shop-orders-table">
                    <thead>
                        <tr><th>№</th><th>Дата</th><th>Сумма</th><th>Статус</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->numberLabel() }}</td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ number_format($order->totalCost(), 0, ',', ' ') }} ₽</td>
                            <td><span class="order-status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                            <td><a href="{{ route('order.show', $order) }}" class="btn btn-sm btn-primary-custom">Подробнее</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <p>У вас пока нет заказов.</p>
                <a href="{{ route('products') }}" class="btn btn-accent">В каталог</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
