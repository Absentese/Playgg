<div class="promo-form mb-3">
    <label class="form-label small mb-2">Промокод</label>
    @if($summary['promo'] ?? null)
    <div class="promo-form__applied d-flex align-items-center justify-content-between gap-2 mb-2">
        <span class="promo-form__badge">
            <i class="fas fa-ticket-alt me-1"></i>{{ $summary['promo']->code }}
            <small class="opacity-75">({{ $summary['promo']->valueLabel() }})</small>
        </span>
        <form id="cartPromoRemoveForm" action="{{ route('cart.promo.remove') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">Убрать</button>
        </form>
    </div>
    @else
    <form id="cartPromoApplyForm" action="{{ route('cart.promo.apply') }}" method="POST" class="promo-form__row">
        @csrf
        <input type="text"
               name="promo_code"
               class="form-control @error('promo_code') is-invalid @enderror"
               value="{{ old('promo_code') }}"
               placeholder="Введите промокод"
               autocomplete="off">
        <button type="submit" class="btn btn-outline-primary">Применить</button>
    </form>
    @error('promo_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @endif
</div>
