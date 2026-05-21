@extends('shop.layouts.app')
@section('title', 'Оформление заказа')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header text-white" style="background: var(--primary);">
                    <h4 class="mb-0">Оформление заказа</h4>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-4 shop-orders-table">
                        <thead><tr><th>Игра</th><th class="text-end">Цена</th></tr></thead>
                        <tbody>
                            @foreach($cart->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">{{ number_format($item->product->price, 0, ',', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @include('shop.partials.promo-form', ['summary' => $summary])
                    <div class="mb-4">
                        @include('shop.partials.checkout-summary', ['summary' => $summary])
                    </div>
                    <p class="shop-form-card__hint mb-3">
                        <i class="fas fa-key me-1"></i>
                        Ключ будет выдан на аккаунт Steam, привязанный к вашему профилю, в течение 5–15 минут после оплаты.
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-user-circle me-1"></i>
                        Поля заполнены из вашего <a href="{{ route('profile.edit') }}">профиля</a>. Измените их здесь или обновите в профиле.
                    </p>
                    <form method="POST" action="{{ route('order.store') }}">
                        @csrf
                        @if($summary['promo'] ?? null)
                        <input type="hidden" name="promo_code" value="{{ $summary['promo']->code }}">
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Имя</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $initial['first_name']) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Фамилия</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $initial['last_name']) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email для чека</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $initial['email']) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="steam_profile_url">Ссылка на профиль Steam</label>
                                <input type="url"
                                       id="steam_profile_url"
                                       name="steam_profile_url"
                                       class="form-control @error('steam_profile_url') is-invalid @enderror"
                                       value="{{ old('steam_profile_url', $initial['steam_profile_url']) }}"
                                       placeholder="https://steamcommunity.com/profiles/76561198...">
                                @error('steam_profile_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Полная ссылка на профиль или custom URL — для проверки аккаунта</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent w-100">Перейти к оплате</button>
                                <a href="{{ route('cart') }}" class="btn btn-outline-secondary w-100 mt-2">В корзину</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
