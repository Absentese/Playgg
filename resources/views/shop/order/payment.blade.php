@extends('shop.layouts.app')
@section('title', 'Оплата')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: var(--primary);">Оплата заказа {{ $order->numberLabel() }}</div>
                <div class="card-body">
                    <div class="alert alert-info">Сумма к оплате: <strong>{{ number_format($order->totalCost(), 0, ',', ' ') }} ₽</strong></div>
                    <form method="POST" action="{{ route('order.payment.process', $order) }}">
                        @csrf
                        <div class="cart-payment-methods mb-3">
                            <label class="cart-payment-method">
                                <input type="radio" name="payment_method" value="card" class="cart-payment-method__input" checked required>
                                <span class="cart-payment-method__box"><i class="fas fa-credit-card"></i><span>Карта</span></span>
                            </label>
                            <label class="cart-payment-method">
                                <input type="radio" name="payment_method" value="sbp" class="cart-payment-method__input">
                                <span class="cart-payment-method__box"><i class="fas fa-qrcode"></i><span>СБП</span></span>
                            </label>
                        </div>
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
                        <button type="submit" class="btn btn-accent w-100"><i class="fas fa-lock me-2"></i>Оплатить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
