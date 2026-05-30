@extends('shop.layouts.app')

@section('title', 'Главная')

@section('content')
<section class="hero-section py-5">
    <div class="hero-section__bg" aria-hidden="true">
        <img src="{{ asset('images/hero-gaming.jpg') }}" alt="">
    </div>
    <div class="container py-5 hero-content">
        <div class="row align-items-center">
            <div class="col-lg-8 col-xl-7">
                <span class="hero-badge mb-3">
                    <i class="fas fa-bolt"></i> Мгновенная доставка ключей
                </span>
                <h1 class="display-4 fw-bold mt-2 mb-3">
                    Покупайте игры<br><span class="hero-highlight">на выгодных ценах</span>
                </h1>
                <p class="lead mb-4">Ключи Steam, Epic и GOG со скидками до 90%. Предзаказы, хиты продаж и пополнение кошелька Steam без комиссии.</p>
                <div class="d-flex flex-wrap gap-3 mb-0">
                    <a href="{{ route('products') }}" class="btn btn-lg btn-glow px-4 rounded-pill">
                        <i class="fas fa-gamepad me-2"></i>В каталог
                    </a>
                    <a href="{{ route('products', ['sale' => 1]) }}" class="btn btn-lg btn-outline-light px-4 rounded-pill">
                        <i class="fas fa-fire me-2"></i>Скидки
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>2000+</strong>
                        <span>игр в каталоге</span>
                    </div>
                    <div class="hero-stat">
                        <strong>5 мин</strong>
                        <span>доставка ключей</span>
                    </div>
                    <div class="hero-stat">
                        <strong>до 90%</strong>
                        <span>скидки на хиты</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($featuredProducts->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="section-title mb-1">Хиты продаж</h2>
                <p class="section-subtitle mb-0">Популярные игры с максимальной скидкой</p>
            </div>
            <a href="{{ route('products') }}" class="btn btn-outline-light rounded-pill">Все игры</a>
        </div>
        @include('shop.partials.products_grid', ['products' => $featuredProducts])
    </div>
</section>
@endif

@if($saleProducts->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title section-title--decor">Лучшие скидки</h2>
            <p class="section-subtitle">Как на SteamPay и GabeStore — выгодно каждый день</p>
        </div>
        @include('shop.partials.products_grid', ['products' => $saleProducts])
    </div>
</section>
@endif

<section id="why-playgg" class="py-5 why-playgg" style="background: var(--bg-elevated);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title section-title--decor">Почему {{ config('app.name') }}</h2>
            <p class="section-subtitle">Мы за честные цены, как у крупных площадок</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="benefit-item text-center p-4 h-100">
                    <div class="benefit-icon"><i class="fas fa-key"></i></div>
                    <h4 class="benefit-item__title">Мгновенные ключи</h4>
                    <p class="benefit-item__text">Ключ приходит на аккаунт Steam за 5–15 минут после оплаты, как на Kupikod и STEAMPAY.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-item text-center p-4 h-100">
                    <div class="benefit-icon benefit-icon--cyan"><i class="fas fa-shield-alt"></i></div>
                    <h4 class="benefit-item__title">Гарантия подлинности</h4>
                    <p class="benefit-item__text">Ключи от официальных дистрибьюторов. Цифровой товар, возврату не подлежит.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-item text-center p-4 h-100">
                    <div class="benefit-icon benefit-icon--indigo"><i class="fab fa-steam"></i></div>
                    <h4 class="benefit-item__title">Пополнение Steam</h4>
                    <p class="benefit-item__text">Пополнение кошелька Steam с комиссией от 0%, как на маркетплейсах IGM и Kupikod.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
