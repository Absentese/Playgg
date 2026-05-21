@extends('shop.layouts.app')

@section('title', 'Каталог')

@section('content')
<div class="page-header">
    <div class="container text-center">
        @include('shop.partials.logo-mark', ['class' => 'logo-mark--lg mb-3'])
        <h1 class="fw-bold mb-2 text-white">Каталог игр</h1>
        <p class="lead mb-0 opacity-90">Более 2000 ключей Steam, Epic и GOG</p>
    </div>
</div>

<div class="container py-5">
    @include('shop.partials.catalog_controls')

    @if($products->isNotEmpty())
        @include('shop.partials.products_grid', ['products' => $products])
    @else
        <div class="content-card text-center py-5">
            <div class="benefit-icon mx-auto mb-4"><i class="fas fa-gamepad"></i></div>
            <h3 class="fw-bold text-white">Игры не найдены</h3>
            <p class="text-muted">Попробуйте другой запрос, фильтр или категорию</p>
            <a href="{{ route('products') }}" class="btn btn-accent rounded-pill px-4 mt-2">Сбросить фильтры</a>
        </div>
    @endif
</div>
@endsection
