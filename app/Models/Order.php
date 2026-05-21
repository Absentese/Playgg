<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Ожидает оплаты',
        'processing' => 'Формирование ключей',
        'shipped' => 'Ключи отправлены',
        'completed' => 'Завершён',
        'cancelled' => 'Отменён',
    ];

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email',
        'steam_id', 'steam_profile_url',
        'status', 'paid', 'payment_method',
        'promo_code_id', 'promo_code',
        'subtotal', 'discount_amount', 'total',
    ];

    protected function casts(): array
    {
        return [
            'paid' => 'boolean',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function itemsSubtotal(): float
    {
        return (float) $this->items->sum(fn (OrderItem $item) => $item->cost());
    }

    public function subtotalAmount(): float
    {
        return (float) ($this->subtotal ?? $this->itemsSubtotal());
    }

    public function discountAmount(): float
    {
        return (float) ($this->discount_amount ?? 0);
    }

    public function totalCost(): float
    {
        if ($this->total !== null) {
            return (float) $this->total;
        }

        return max(0, $this->subtotalAmount() - $this->discountAmount());
    }

    public function hasDiscount(): bool
    {
        return $this->discountAmount() > 0;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Порядковый номер заказа для отображения (1, 2, 3…), без пропусков после удаления. */
    public function number(): int
    {
        return (int) static::where('id', '<=', $this->id)->count();
    }

    public function numberLabel(): string
    {
        return '#'.$this->number();
    }

    public function steamCommunityUrl(): string
    {
        if ($this->steam_profile_url) {
            return $this->steam_profile_url;
        }

        return 'https://steamcommunity.com/profiles/'.$this->steam_id;
    }
}
