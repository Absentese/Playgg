@extends('shop.layouts.app')

@section('title', 'Гарантия')

@section('content')
<div class="page-header">
    <div class="container text-center py-2">
        <img src="{{ asset('images/icon-guarantee.svg') }}" alt="" width="36" height="36" class="mb-3">
        <h1 class="display-6 fw-bold mb-2 text-white">Гарантия подлинности</h1>
        <p class="lead mb-0 opacity-90">Только ключи от официальных дистрибьюторов</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card">
                <p class="lead text-white">Покупая в playgg, вы получаете лицензионный ключ с гарантией активации.</p>
                <ul class="text-muted">
                    <li class="mb-2">Ключи от официальных поставщиков и региональных дистрибьюторов</li>
                    <li class="mb-2">Все транзакции защищены и проходят через безопасную оплату</li>
                    <li class="mb-2">Цифровой товар, возврату не подлежит</li>
                    <li class="mb-2">Поддержка 24/7</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
