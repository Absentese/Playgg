<!DOCTYPE html>
<html lang="ru">
<head>
    <script>(function(){try{var t=localStorage.getItem('playgg-theme');if(t==='light'||t==='dark')document.documentElement.setAttribute('data-theme',t);}catch(e){}})();</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Админ-панель') — playgg</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800&family=Orbitron:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                @include('shop.partials.logo-mark')
            </a>
        </div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Дашборд
            </a>
            <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Пользователи
            </a>
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Заказы
            </a>
            <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-gamepad"></i> Игры
            </a>
            <a href="{{ route('admin.promo-codes.index') }}" class="admin-nav-link {{ request()->routeIs('admin.promo-codes.*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Промокоды
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="admin-nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> Сообщения
            </a>
            <hr class="border-secondary my-3">
            <a href="{{ route('index') }}" class="admin-nav-link">
                <i class="fas fa-store"></i> На сайт
            </a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title mb-0">@yield('page_title', 'Панель управления')</h1>
            <div class="d-flex align-items-center gap-3">
                @include('shop.partials.theme_toggle')
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Выйти</button>
                </form>
            </div>
        </header>
        <div class="admin-content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('scripts/theme.js') }}"></script>
<script src="{{ asset('scripts/phone-mask.js') }}"></script>
@stack('scripts')
</body>
</html>
