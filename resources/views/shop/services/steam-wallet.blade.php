@extends('shop.layouts.app')

@section('title', 'Пополнение Steam')

@section('content')
<div class="page-header">
    <div class="container text-center py-2">
        <div class="shop-page-icon mx-auto mb-3"><i class="fab fa-steam"></i></div>
        <h1 class="display-6 fw-bold mb-2 text-white">Пополнение Steam</h1>
        <p class="lead mb-0 opacity-90">По Steam ID · комиссия от 0% · зачисление 1–5 минут</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">
            <div class="content-card shop-form-card">
                <h2 class="h5 fw-bold text-white mb-1">Пополнить кошелёк</h2>
                <p class="shop-form-card__hint mb-4">Укажите Steam ID (17 цифр) и сумму. ID можно найти в профиле Steam → «Об аккаунте».</p>

                @auth
                <form method="POST" action="{{ route('services.steam-wallet.store') }}" class="steam-topup-form" id="steamTopupForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="steam_id">Steam ID</label>
                        <input type="text"
                               id="steam_id"
                               name="steam_id"
                               class="form-control @error('steam_id') is-invalid @enderror"
                               value="{{ old('steam_id', $checkout['steam_id'] ?? '') }}"
                               inputmode="numeric"
                               autocomplete="off"
                               maxlength="17"
                               pattern="\d{17}"
                               placeholder="76561198000000000"
                               required>
                        @error('steam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Только цифры, без пробелов и дефисов</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email для чека</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $checkout['email'] ?? auth()->user()->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="amount">Сумма, ₽</label>
                        <input type="number" id="amount" name="amount" min="100" max="15000" step="50"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', 500) }}" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="steam-amount-presets mt-2">
                            @foreach($presetAmounts as $sum)
                            <button type="button" class="steam-amount-preset" data-amount="{{ $sum }}">{{ number_format($sum, 0, ',', ' ') }} ₽</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="steam-topup-summary mb-4" id="steamTopupSummary">
                        <span>К зачислению на Steam</span>
                        <strong id="steamTopupTotal">{{ number_format(old('amount', 500), 0, ',', ' ') }} ₽</strong>
                    </div>

                    <button type="submit" class="btn btn-glow w-100 rounded-pill py-2">
                        <i class="fas fa-wallet me-2"></i>Перейти к оплате
                    </button>
                </form>
                @else
                <div class="shop-auth-prompt text-center py-4">
                    <p class="text-muted mb-3">Войдите в аккаунт, чтобы пополнить кошелёк Steam по ID.</p>
                    <a href="{{ route('login') }}" class="btn btn-glow rounded-pill px-4 me-2">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light rounded-pill px-4">Регистрация</a>
                </div>
                @endauth
            </div>
        </div>

        <div class="col-lg-5">
            <div class="content-card h-100">
                <h3 class="h6 fw-bold text-white mb-3">Как узнать Steam ID</h3>
                <ol class="shop-steps mb-4">
                    <li>Откройте клиент Steam или сайт store.steampowered.com</li>
                    <li>Профиль → «Об аккаунте»</li>
                    <li>Скопируйте поле <strong class="text-white">Steam ID</strong> (17 цифр)</li>
                </ol>
                <ul class="shop-feature-list mb-0">
                    <li><i class="fas fa-check-circle text-accent"></i> Минимум 100 ₽</li>
                    <li><i class="fas fa-check-circle text-accent"></i> Комиссия 0% от 500 ₽</li>
                    <li><i class="fas fa-check-circle text-accent"></i> Оплата картой или СБП</li>
                    <li><i class="fas fa-check-circle text-accent"></i> Поддержка 24/7 в чате</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.steam-amount-preset').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById('amount');
        input.value = btn.dataset.amount;
        input.dispatchEvent(new Event('input'));
        document.querySelectorAll('.steam-amount-preset').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
    });
});
const amountInput = document.getElementById('amount');
const totalEl = document.getElementById('steamTopupTotal');
if (amountInput && totalEl) {
    amountInput.addEventListener('input', () => {
        const v = parseInt(amountInput.value, 10) || 0;
        totalEl.textContent = v.toLocaleString('ru-RU') + ' ₽';
    });
}
const steamIdInput = document.getElementById('steam_id');
if (steamIdInput) {
    steamIdInput.addEventListener('input', () => {
        steamIdInput.value = steamIdInput.value.replace(/\D/g, '').slice(0, 17);
    });
}
</script>
@endpush
