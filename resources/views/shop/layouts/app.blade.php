<!DOCTYPE html>
<html lang="ru">
<head>
    <script>(function(){try{var t=localStorage.getItem('playgg-theme');if(t==='light'||t==='dark')document.documentElement.setAttribute('data-theme',t);}catch(e){}})();</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name')) — интернет-магазин цифровых игр</title>
    <meta name="description" content="{{ config('app.name') }} — ключи к играм Steam, Epic, GOG. Мгновенная доставка, скидки до 90%, пополнение кошелька Steam.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}@if(file_exists(public_path('css/style.css')))?v={{ filemtime(public_path('css/style.css')) }}@endif">
    @stack('styles')
</head>
<body>
<header class="site-header sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark site-navbar">
            @include('shop.partials.logo')
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Меню">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="{{ route('products') }}"><i class="fas fa-gamepad me-1 opacity-75"></i> Каталог</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('products', ['sale' => 1]) }}"><i class="fas fa-fire me-1 opacity-75"></i> Скидки</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('services.steam-wallet') }}"><i class="fab fa-steam me-1 opacity-75"></i> Steam</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('about') }}"><i class="fas fa-info-circle me-1 opacity-75"></i> О нас</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contacts') }}"><i class="fas fa-envelope me-1 opacity-75"></i> Контакты</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @include('shop.partials.theme_toggle')
                    @auth
                    <a href="{{ route('cart') }}" class="btn btn-outline-light position-relative rounded-pill px-3">
                        <i class="fas fa-shopping-cart"></i>
                        @if($cartItemsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $cartItemsCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-id-card me-2 text-muted"></i> Профиль</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders') }}"><i class="fas fa-key me-2 text-muted"></i> Мои заказы</a></li>
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-shield-alt me-2 text-accent"></i> Админ-панель</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">@csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Выйти</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-glow rounded-pill px-4">Войти</a>
                    @endauth
                </div>
            </div>
        </nav>
        <div class="site-header__search-row">
            @include('shop.partials.navbar_search')
        </div>
    </div>
</header>

@include('shop.partials.feature_strip')

<main>
    @include('shop.partials.alerts')
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container">
        <div class="row g-4 g-lg-5 footer-main">
            <div class="col-lg-4 footer-brand">
                @include('shop.partials.logo-mark', ['class' => 'logo-mark--footer'])
                <p class="footer-brand__text small opacity-75 mb-0">Магазин цифровых игр и ключей. Мгновенная доставка, честные скидки, поддержка 24/7.</p>
            </div>
            <div class="col-6 col-md-4 col-lg-2 footer-col">
                <h5 class="footer-col__title">Услуги</h5>
                <ul class="footer-links list-unstyled mb-0">
                    <li><a href="{{ route('services.delivery') }}">Доставка ключей</a></li>
                    <li><a href="{{ route('services.guarantee') }}">Гарантия</a></li>
                    <li><a href="{{ route('services.steam-wallet') }}">Пополнение Steam</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4 col-lg-3 footer-col">
                <h5 class="footer-col__title">Контакты</h5>
                <ul class="footer-contact list-unstyled mb-0">
                    <li><i class="fas fa-envelope text-accent" aria-hidden="true"></i><a href="mailto:info@playgg.ru">info@playgg.ru</a></li>
                    <li><i class="fas fa-phone text-accent" aria-hidden="true"></i><span>+7 (999) 100-20-30</span></li>
                    <li><i class="fas fa-clock text-accent" aria-hidden="true"></i><span>Поддержка 24/7</span></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-3 footer-col footer-social">
                <h5 class="footer-col__title">Мы в соцсетях</h5>
                <div class="social-links">
                    <a href="https://vk.com" target="_blank" rel="noopener noreferrer" aria-label="ВКонтакте"><i class="fab fa-vk"></i></a>
                    <a href="https://store.steampowered.com" target="_blank" rel="noopener noreferrer" class="social-steam" aria-label="Steam"><i class="fab fa-steam"></i></a>
                    <a href="https://max.ru" target="_blank" rel="noopener noreferrer" class="social-max" aria-label="Мессенджер MAX">
                        <img src="{{ asset('images/icon-max.svg') }}" alt="" width="22" height="22">
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-bottom__links small opacity-75 mb-2">
                <a href="{{ route('privacy') }}">Политика конфиденциальности</a>
                <span class="footer-bottom__sep" aria-hidden="true">|</span>
                <a href="{{ route('offer') }}">Публичная оферта</a>
            </p>
            <p class="footer-bottom__copy small opacity-75 mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('scripts/theme.js') }}"></script>
<script src="{{ asset('scripts/support-chat.js') }}"></script>
<script src="{{ asset('scripts/phone-mask.js') }}"></script>
@stack('scripts')
</body>
</html>
