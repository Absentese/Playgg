@php
    $subtotal = $summary['subtotal'] ?? 0;
    $discount = $summary['discount'] ?? 0;
    $total = $summary['total'] ?? $subtotal;
    $promo = $summary['promo'] ?? null;
@endphp

<div class="checkout-summary">
    <p class="d-flex justify-content-between mb-2">
        <span>Сумма игр</span>
        <strong>{{ number_format($subtotal, 0, ',', ' ') }} ₽</strong>
    </p>
    @if($discount > 0)
    <p class="d-flex justify-content-between mb-2 checkout-summary__discount">
        <span>
            Скидка
            @if($promo)
            <span class="checkout-summary__code">{{ $promo->code }}</span>
            @endif
        </span>
        <strong>−{{ number_format($discount, 0, ',', ' ') }} ₽</strong>
    </p>
    @endif
    <hr class="checkout-summary__hr">
    <p class="d-flex justify-content-between checkout-summary__total mb-0">
        <span>К оплате</span>
        <strong>{{ number_format($total, 0, ',', ' ') }} ₽</strong>
    </p>
</div>
