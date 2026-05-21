@extends('shop.layouts.app')
@section('title', 'Оплата пополнения Steam')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="content-card shop-form-card">
                <h1 class="h5 fw-bold text-white mb-3">Оплата пополнения #{{ $topup->id }}</h1>
                <ul class="shop-payment-details mb-4">
                    <li><span>Steam ID</span><strong>{{ $topup->formattedSteamId() }}</strong></li>
                    <li><span>Email</span><strong>{{ $topup->email }}</strong></li>
                    <li><span>Сумма</span><strong class="text-accent-cyan">{{ number_format($topup->amount, 0, ',', ' ') }} ₽</strong></li>
                </ul>
                <form method="POST" action="{{ route('services.steam-wallet.payment.process', $topup) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Номер карты</label>
                        <input type="text" class="form-control" name="card_number" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Имя владельца</label>
                        <input type="text" class="form-control" name="card_name" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Срок</label>
                            <input type="text" class="form-control" name="card_expiry" placeholder="MM/YY" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" name="card_cvv" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-glow w-100 rounded-pill py-2">
                        <i class="fas fa-lock me-2"></i>Оплатить {{ number_format($topup->amount, 0, ',', ' ') }} ₽
                    </button>
                </form>
                <a href="{{ route('services.steam-wallet') }}" class="btn btn-link text-muted mt-3 px-0">← Изменить данные</a>
            </div>
        </div>
    </div>
</div>
@endsection
