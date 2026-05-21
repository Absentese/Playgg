@extends('shop.layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products') }}">Каталог</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="product-photo-wrap product-photo-wrap--detail">
                    <img src="{{ $product->imageUrl() }}" class="product-photo @if($product->hasWhiteMatteBackground()) product-photo--matte @endif" alt="{{ $product->name }}" style="max-height: 400px;">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4 shop-product-panel">
                    <h1>{{ $product->name }}</h1>
                    <div class="shop-product-meta">
                        @foreach($product->displayTags() as $tag)
                        @if($tag['url'])
                        <a href="{{ $tag['url'] }}" class="badge text-decoration-none">{{ $tag['label'] }}</a>
                        @else
                        <span class="badge">{{ $tag['label'] }}</span>
                        @endif
                        @endforeach
                        <span class="badge"><i class="fab fa-steam me-1"></i>{{ $product->platform }}</span>
                        @if($product->hasDiscount())
                        <span class="badge badge-deal">-{{ $product->discountPercent() }}%</span>
                        @endif
                    </div>
                    <p class="shop-product-price mb-4">
                        {{ number_format($product->price, 0, ',', ' ') }} ₽
                        @if($product->hasDiscount())
                        <span class="product-price-old ms-2">{{ number_format($product->old_price, 0, ',', ' ') }} ₽</span>
                        @endif
                    </p>
                    <h5 class="fw-semibold mb-2">Описание</h5>
                    <p class="shop-product-description">{{ $product->description }}</p>
                    @auth
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-accent w-100"><i class="fas fa-shopping-cart me-2"></i>В корзину</button>
                    </form>
                    @else
                    <div class="alert alert-info mt-4"><a href="{{ route('login') }}">Войдите</a>, чтобы добавить игру в корзину</div>
                    @endauth
                    <div class="mt-4 p-3 rounded shop-product-note">
                        <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>В наличии</p>
                        <p class="mb-1"><i class="fab fa-steam me-2"></i>Ключ на аккаунт Steam за 5–15 минут</p>
                        <p class="mb-0"><i class="fas fa-ban me-2"></i>Цифровой товар, возврату не подлежит</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($relatedProducts->isNotEmpty())
    <section class="mt-5">
        <h3 class="shop-section-title mb-4">Похожие игры</h3>
        @include('shop.partials.products_grid', ['products' => $relatedProducts])
    </section>
    @endif
</div>
@endsection
