<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PromoCodeService
{
    private const SESSION_KEY = 'applied_promo_code_id';

    public function summaryForCart(Cart $cart, User $user): array
    {
        $subtotal = $cart->totalPrice();
        $promo = $this->getApplied($user, $subtotal);

        return $this->buildSummary($subtotal, $promo);
    }

    /** @return array{subtotal: float, discount: float, total: float, promo: ?PromoCode, code: ?string} */
    public function buildSummary(float $subtotal, ?PromoCode $promo): array
    {
        $discount = $promo ? $this->calculateDiscount($promo, $subtotal) : 0.0;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, round($subtotal - $discount, 2)),
            'promo' => $promo,
            'code' => $promo?->code,
        ];
    }

    public function apply(string $rawCode, User $user, float $subtotal): PromoCode
    {
        $promo = $this->resolve($rawCode, $user, $subtotal);
        session([self::SESSION_KEY => $promo->id]);

        return $promo;
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function getApplied(?User $user, float $subtotal): ?PromoCode
    {
        $id = session(self::SESSION_KEY);
        if (! $id) {
            return null;
        }

        $promo = PromoCode::find($id);
        if (! $promo) {
            $this->clear();

            return null;
        }

        try {
            $this->assertValid($promo, $user, $subtotal);
        } catch (ValidationException) {
            $this->clear();

            return null;
        }

        return $promo;
    }

    public function resolve(string $rawCode, ?User $user, float $subtotal): PromoCode
    {
        $code = PromoCode::normalizeCode($rawCode);
        $promo = PromoCode::where('code', $code)->first();

        if (! $promo) {
            throw ValidationException::withMessages([
                'promo_code' => 'Промокод не найден.',
            ]);
        }

        $this->assertValid($promo, $user, $subtotal);

        return $promo;
    }

    public function calculateDiscount(PromoCode $promo, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        $discount = $promo->type === PromoCode::TYPE_FIXED
            ? (float) $promo->value
            : round($subtotal * ((float) $promo->value / 100), 2);

        return min($discount, $subtotal);
    }

    public function assertValid(PromoCode $promo, ?User $user, float $subtotal): void
    {
        if (! $promo->is_active) {
            throw ValidationException::withMessages([
                'promo_code' => 'Промокод недоступен.',
            ]);
        }

        $now = now();

        if ($promo->starts_at && $promo->starts_at->gt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => 'Промокод будет активен с '.$promo->starts_at
                    ->timezone(config('app.timezone'))
                    ->format('d.m.Y H:i').' (МСК).',
            ]);
        }

        if ($promo->expires_at && $promo->expires_at->lt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => 'Срок действия промокода истёк '.$promo->expires_at
                    ->timezone(config('app.timezone'))
                    ->format('d.m.Y H:i').' (МСК).',
            ]);
        }

        if ($promo->max_uses !== null && $promo->used_count >= $promo->max_uses) {
            throw ValidationException::withMessages([
                'promo_code' => 'Лимит использований промокода исчерпан.',
            ]);
        }

        if ($promo->min_order_amount && $subtotal < (float) $promo->min_order_amount) {
            throw ValidationException::withMessages([
                'promo_code' => 'Минимальная сумма заказа для промокода — '
                    .number_format($promo->min_order_amount, 0, ',', ' ').' ₽.',
            ]);
        }
    }

    public function markUsed(PromoCode $promo): void
    {
        $promo->increment('used_count');
    }
}
