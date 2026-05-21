@extends('shop.layouts.app')
@section('title', 'Корзина')
@section('content')
<div class="container py-5 cart-page">
    <div class="text-center mb-4">
        <h1 class="shop-page-title"><i class="fas fa-shopping-cart me-2"></i>Корзина</h1>
        <p class="lead">Выбранные игры и оплата</p>
    </div>
    @if($cart->items->isNotEmpty())
        @include('shop.partials.cart-checkout-form', compact('cart', 'summary', 'initial'))
    @else
    <div class="card cart-empty-card text-center py-5">
        <div class="card-body">
            <i class="fas fa-shopping-cart fa-4x mb-3" style="color: var(--accent);"></i>
            <h3>Корзина пуста</h3>
            <a href="{{ route('products') }}" class="btn btn-accent mt-3">В каталог</a>
        </div>
    </div>
    @endif
</div>
@endsection
