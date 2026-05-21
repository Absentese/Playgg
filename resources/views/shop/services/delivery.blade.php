@extends('shop.layouts.app')

@section('title', 'Доставка ключей')

@section('content')
<div class="page-header">
    <div class="container text-center py-2">
        <img src="{{ asset('images/icon-keys.svg') }}" alt="" width="36" height="36" class="mb-3">
        <h1 class="display-6 fw-bold mb-2 text-white">Доставка ключей</h1>
        <p class="lead mb-0 opacity-90">Мгновенно на аккаунт Steam после оплаты</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card">
                <p class="lead text-white">Цифровые ключи на playgg доставляются автоматически и не требуют ожидания курьера.</p>
                <ol class="shop-steps mb-0">
                    <li class="mb-2">Оформите заказ и укажите <strong class="text-white">Steam ID</strong> (17 цифр из «Об аккаунте»)</li>
                    <li class="mb-2">Завершите оплату картой или СБП</li>
                    <li class="mb-2">В течение 5–15 минут ключ придёт на аккаунт Steam и в раздел «Мои заказы»</li>
                    <li class="mb-2">Активируйте ключ в Steam → «Добавить игру» → «Активировать в Steam»</li>
                </ol>
                <p class="text-muted mb-0">При задержке пишите в онлайн-чат или на info@playgg.ru.</p>
            </div>
        </div>
    </div>
</div>
@endsection
