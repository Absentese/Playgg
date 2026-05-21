<form method="POST"
      action="{{ route('order.store') }}"
      class="cart-checkout-form visually-hidden"
      id="cartCheckoutForm"
      aria-hidden="true"
      tabindex="-1"
      data-details-errors="{{ $errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('steam_profile_url') ? '1' : '0' }}">
    @csrf
    @if($summary['promo'] ?? null)
    <input type="hidden" name="promo_code" value="{{ $summary['promo']->code }}">
    @endif
</form>

<div class="cart-checkout-layout">
    <div class="row g-4">
        <div class="col-lg-8">
            <ul class="nav cart-tabs" id="cartTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cart-tab-games" data-bs-toggle="tab" data-bs-target="#cart-pane-games" type="button" role="tab" aria-controls="cart-pane-games" aria-selected="true">
                        <i class="fas fa-gamepad me-1"></i>Игры <span class="cart-tabs__count">{{ $cart->items->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cart-tab-details" data-bs-toggle="tab" data-bs-target="#cart-pane-details" type="button" role="tab" aria-controls="cart-pane-details" aria-selected="false">
                        <i class="fas fa-user me-1"></i>Данные заказа
                    </button>
                </li>
            </ul>

            <div class="tab-content cart-tab-content mt-3">
                <div class="tab-pane fade show active" id="cart-pane-games" role="tabpanel" aria-labelledby="cart-tab-games">
                    <div class="cart-items-card">
                        <div class="cart-items-card__head">
                            <span>Игра</span>
                            <span>Цена</span>
                        </div>
                        <ul class="cart-items-list list-unstyled mb-0">
                            @foreach($cart->items as $item)
                            <li class="cart-item">
                                <div class="cart-item__main">
                                    <a href="{{ route('product.show', $item->product) }}" class="cart-item__media product-photo-wrap product-photo-wrap--cart">
                                        <img src="{{ $item->product->imageUrl() }}" class="product-photo @if($item->product->hasWhiteMatteBackground()) product-photo--matte @endif" alt="{{ $item->product->name }}">
                                    </a>
                                    <div class="cart-item__info">
                                        <a href="{{ route('product.show', $item->product) }}" class="cart-item__title">{{ $item->product->name }}</a>
                                        <p class="cart-item__meta">
                                            <span>{{ $item->product->category->name }}</span>
                                            <span class="cart-item__meta-sep">·</span>
                                            <span><i class="fab fa-steam me-1"></i>{{ $item->product->platform }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="cart-item__actions">
                                    <span class="cart-item__price">{{ number_format($item->product->price, 0, ',', ' ') }} ₽</span>
                                    <button type="button"
                                            class="btn btn-sm btn-danger cart-item__remove"
                                            title="Удалить из корзины"
                                            aria-label="Удалить из корзины"
                                            form="cart-remove-{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="cart-delivery-note small text-muted mt-3 mb-0">
                        <i class="fab fa-steam me-1"></i>Ключи выдаются на аккаунт Steam в течение 5–15 минут после оплаты.
                        <span class="cart-delivery-note__sep">·</span>
                        <i class="fas fa-ban me-1"></i>Цифровой товар, возврату не подлежит.
                    </p>
                </div>

                <div class="tab-pane fade" id="cart-pane-details" role="tabpanel" aria-labelledby="cart-tab-details">
                    <div class="cart-details-card">
                        <p class="cart-details-card__hint small text-muted mb-3">
                            <i class="fas fa-user-circle me-1"></i>
                            Данные из <a href="{{ route('profile.edit') }}">профиля</a>. Ключ выдаётся на привязанный аккаунт Steam.
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Имя</label>
                                <input type="text" name="first_name" form="cartCheckoutForm" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $initial['first_name']) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Фамилия</label>
                                <input type="text" name="last_name" form="cartCheckoutForm" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $initial['last_name']) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email для чека</label>
                                <input type="email" name="email" form="cartCheckoutForm" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $initial['email']) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="cart_steam_profile_url">Ссылка на профиль Steam</label>
                                <input type="url"
                                       id="cart_steam_profile_url"
                                       name="steam_profile_url"
                                       form="cartCheckoutForm"
                                       class="form-control @error('steam_profile_url') is-invalid @enderror"
                                       value="{{ old('steam_profile_url', $initial['steam_profile_url']) }}"
                                       placeholder="https://steamcommunity.com/profiles/76561198...">
                                @error('steam_profile_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Полная ссылка на профиль или custom URL — для проверки аккаунта</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card cart-summary-card">
                <div class="card-body">
                    <h5 class="cart-summary-card__title">Итог заказа</h5>
                    @include('shop.partials.promo-form', ['summary' => $summary])
                    @include('shop.partials.checkout-summary', ['summary' => $summary])

                    <div class="cart-payment-section mt-3">
                        <label class="form-label small mb-2">Способ оплаты</label>
                        <div class="cart-payment-methods" role="group" aria-label="Способ оплаты">
                            <label class="cart-payment-method">
                                <input type="radio" name="payment_method" form="cartCheckoutForm" value="card" class="cart-payment-method__input" {{ old('payment_method', 'card') === 'card' ? 'checked' : '' }} required>
                                <span class="cart-payment-method__box">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Карта</span>
                                </span>
                            </label>
                            <label class="cart-payment-method">
                                <input type="radio" name="payment_method" form="cartCheckoutForm" value="sbp" class="cart-payment-method__input" {{ old('payment_method') === 'sbp' ? 'checked' : '' }}>
                                <span class="cart-payment-method__box">
                                    <i class="fas fa-qrcode"></i>
                                    <span>СБП</span>
                                </span>
                            </label>
                        </div>

                        <div class="cart-card-panel" id="cartCardPanel" @if(old('payment_method') === 'sbp') hidden @endif>
                            <div class="cart-card-panel__head">
                                <i class="fas fa-credit-card"></i>
                                <span>Данные карты</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Номер карты</label>
                                <input type="text" name="card_number" form="cartCheckoutForm" class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number') }}" placeholder="0000 0000 0000 0000" inputmode="numeric" autocomplete="cc-number" data-card-required>
                                @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Имя владельца</label>
                                <input type="text" name="card_name" form="cartCheckoutForm" class="form-control @error('card_name') is-invalid @enderror" value="{{ old('card_name') }}" placeholder="IVAN IVANOV" autocomplete="cc-name" data-card-required>
                                @error('card_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Срок действия</label>
                                    <input type="text" name="card_expiry" form="cartCheckoutForm" class="form-control @error('card_expiry') is-invalid @enderror" value="{{ old('card_expiry') }}" placeholder="MM/YY" autocomplete="cc-exp" data-card-required>
                                    @error('card_expiry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label">CVV</label>
                                    <input type="password" name="card_cvv" form="cartCheckoutForm" class="form-control @error('card_cvv') is-invalid @enderror" value="{{ old('card_cvv') }}" placeholder="•••" maxlength="4" autocomplete="cc-csc" data-card-required>
                                    @error('card_cvv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="cart-sbp-panel" id="cartSbpPanel" @if(old('payment_method', 'card') !== 'sbp') hidden @endif>
                            <div class="cart-sbp-panel__icon"><i class="fas fa-qrcode"></i></div>
                            <p class="cart-sbp-panel__title">Оплата через СБП</p>
                            <p class="cart-sbp-panel__text small text-muted mb-0">После нажатия «Оплата заказа» откроется подтверждение в приложении банка. Платёж зачисляется мгновенно.</p>
                        </div>
                    </div>

                    <p class="cart-summary-note small text-muted mb-3 mt-3">
                        <i class="fab fa-steam me-1"></i>Выдача на аккаунт Steam
                    </p>

                    <button type="submit" form="cartCheckoutForm" class="btn btn-accent w-100 mb-2" id="cartPayBtn">
                        <i class="fas fa-lock me-2"></i>Оплата заказа · {{ number_format($summary['total'] ?? $summary['subtotal'], 0, ',', ' ') }} ₽
                    </button>
                    <a href="{{ route('products') }}" class="btn btn-outline-primary w-100">Продолжить покупки</a>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($cart->items as $item)
<form id="cart-remove-{{ $item->id }}" action="{{ route('cart.remove', $item) }}" method="POST" class="d-none">
    @csrf
</form>
@endforeach

@push('scripts')
@verbatim
<script>
(function () {
    const form = document.getElementById('cartCheckoutForm');
    if (!form) return;

    const cardPanel = document.getElementById('cartCardPanel');
    const sbpPanel = document.getElementById('cartSbpPanel');
    const formId = 'cartCheckoutForm';
    const fieldSelector = (sel) => document.querySelector(sel);
    const formFields = (sel) => document.querySelectorAll(`[form="${formId}"]${sel}`);
    const methodInputs = formFields('.cart-payment-method__input');
    const cardFields = formFields('[data-card-required]');
    const detailsTabBtn = document.getElementById('cart-tab-details');
    const hasDetailsErrors = form.dataset.detailsErrors === '1';

    function setCardFieldsRequired(required) {
        cardFields.forEach((el) => {
            el.required = required;
            if (!required) el.removeAttribute('required');
        });
    }

    function updatePaymentPanels() {
        const checked = document.querySelector(`[form="${formId}"].cart-payment-method__input:checked`);
        const method = checked?.value || 'card';
        const isCard = method === 'card';
        if (cardPanel) cardPanel.hidden = !isCard;
        if (sbpPanel) sbpPanel.hidden = isCard;
        setCardFieldsRequired(isCard);
    }

    methodInputs.forEach((input) => input.addEventListener('change', updatePaymentPanels));
    updatePaymentPanels();

    if (hasDetailsErrors && detailsTabBtn) {
        bootstrap.Tab.getOrCreateInstance(detailsTabBtn).show();
    }

    form.addEventListener('submit', (e) => {
        const missing = ['first_name', 'last_name', 'email'].filter((name) => !fieldSelector(`[form="${formId}"][name="${name}"]`)?.value?.trim());
        if (missing.length && detailsTabBtn) {
            e.preventDefault();
            bootstrap.Tab.getOrCreateInstance(detailsTabBtn).show();
            fieldSelector(`[form="${formId}"][name="${missing[0]}"]`)?.focus();
        }
    });

    const cardNumber = fieldSelector(`[form="${formId}"][name="card_number"]`);
    if (cardNumber) {
        cardNumber.addEventListener('input', () => {
            const digits = cardNumber.value.replace(/\D/g, '').slice(0, 16);
            cardNumber.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });
    }

    const cardExpiry = fieldSelector(`[form="${formId}"][name="card_expiry"]`);
    if (cardExpiry) {
        cardExpiry.addEventListener('input', () => {
            let v = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
            cardExpiry.value = v;
        });
    }
})();
</script>
@endverbatim
@endpush
