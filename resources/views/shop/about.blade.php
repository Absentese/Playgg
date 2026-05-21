@extends('shop.layouts.app')

@section('title', 'О нас')

@section('content')
<div class="page-header">
    <div class="container text-center py-4">
        @include('shop.partials.logo-mark', ['class' => 'logo-mark--lg mb-3'])
        <h1 class="display-6 fw-bold mb-2 text-white">О {{ config('app.name') }}</h1>
        <p class="lead mb-0 opacity-90">Интернет-магазин цифровых видеоигр с мгновенной доставкой ключей</p>
    </div>
</div>

<section class="py-5 shop-page-section">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <div class="content-card">
                    <h2 class="h4 fw-bold text-white mb-3">Наша миссия</h2>
                    <p class="shop-body-text">{{ config('app.name') }} — современная площадка для геймеров. Мы вдохновлены сервисами Kupikod, STEAMPAY, GabeStore и IGM: честные цены, мгновенная доставка и удобный каталог.</p>
                    <p class="shop-body-text mb-0">В ассортименте — ключи Steam, Epic Games, GOG, предзаказы хитов и пополнение кошелька Steam по ID.</p>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="about-visual">
                    <img src="{{ asset('images/about-gaming.jpg') }}" class="about-visual__img" alt="Игровой каталог {{ config('app.name') }}" loading="lazy" width="960" height="540">
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="content-card shop-stat-card text-center h-100">
                    <div class="shop-stat-card__value">2000+</div>
                    <p class="shop-stat-card__label mb-0">игр в каталоге</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card shop-stat-card text-center h-100">
                    <div class="shop-stat-card__value">5 мин</div>
                    <p class="shop-stat-card__label mb-0">доставка ключей</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content-card shop-stat-card text-center h-100">
                    <div class="shop-stat-card__value">24/7</div>
                    <p class="shop-stat-card__label mb-0">поддержка игроков</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="content-card h-100">
                    <h3 class="h6 fw-bold text-white mb-3"><i class="fas fa-bolt text-accent me-2"></i>Почему мы</h3>
                    <ul class="shop-feature-list mb-0">
                        <li><i class="fas fa-check-circle text-accent"></i> Мгновенная выдача ключей на Steam</li>
                        <li><i class="fas fa-check-circle text-accent"></i> Честные скидки на хиты Steam</li>
                        <li><i class="fas fa-check-circle text-accent"></i> Пополнение кошелька по Steam ID</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="content-card h-100">
                    <h3 class="h6 fw-bold text-white mb-3"><i class="fas fa-shield-alt text-accent-cyan me-2"></i>Гарантии</h3>
                    <ul class="shop-feature-list mb-4">
                        <li><i class="fas fa-check-circle text-accent"></i> Ключи от официальных поставщиков</li>
                        <li><i class="fas fa-check-circle text-accent"></i> Цифровой товар, возврату не подлежит</li>
                        <li><i class="fas fa-check-circle text-accent"></i> Безопасная оплата картой и СБП</li>
                    </ul>
                    <a href="{{ route('products') }}" class="btn btn-glow rounded-pill px-4">Перейти в каталог</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
