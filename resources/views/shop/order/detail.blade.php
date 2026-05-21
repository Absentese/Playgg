@extends('shop.layouts.app')
@section('title', 'Заказ '.$order->numberLabel())
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: var(--primary);">Заказ {{ $order->numberLabel() }}</div>
                <div class="card-body">
                    <table class="table">
                        <thead><tr><th>Игра</th><th class="text-end">Цена</th></tr></thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">{{ number_format($item->price, 0, ',', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td class="text-end">Сумма</td><td class="text-end">{{ number_format($order->subtotalAmount(), 0, ',', ' ') }} ₽</td></tr>
                            @if($order->hasDiscount())
                            <tr><td class="text-end">Скидка @if($order->promo_code)({{ $order->promo_code }})@endif</td><td class="text-end text-success">−{{ number_format($order->discountAmount(), 0, ',', ' ') }} ₽</td></tr>
                            @endif
                            <tr><td class="text-end fw-bold">К оплате</td><td class="text-end fw-bold">{{ number_format($order->totalCost(), 0, ',', ' ') }} ₽</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <p class="mb-2"><strong>Статус:</strong>
                        <span class="order-status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span>
                    </p>
                    <p><strong>Оплата:</strong> {{ $order->paid ? 'Оплачен' : 'Не оплачен' }}</p>
                    <p><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                    @if(!$order->paid)
                    <a href="{{ route('order.payment', $order) }}" class="btn btn-accent w-100">Оплата заказа</a>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6><i class="fab fa-steam me-1"></i> Аккаунт Steam</h6>
                    <p class="small mb-1"><strong>Steam ID:</strong> <span class="font-monospace">{{ $order->steam_id }}</span></p>
                    @if($order->steam_profile_url)
                    <p class="small mb-1"><strong>Профиль:</strong> <a href="{{ $order->steam_profile_url }}" target="_blank" rel="noopener" class="text-break">{{ $order->steam_profile_url }}</a></p>
                    @else
                    <p class="small mb-1"><strong>Профиль:</strong> <a href="{{ $order->steamCommunityUrl() }}" target="_blank" rel="noopener">steamcommunity.com</a></p>
                    @endif
                    <p class="small mb-0 text-muted">{{ $order->first_name }} {{ $order->last_name }} · {{ $order->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
