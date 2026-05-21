<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function normalizeCode(string $code): string
    {
        return mb_strtoupper(trim($code));
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_FIXED => 'Фиксированная',
            default => 'Процент',
        };
    }

    public function valueLabel(): string
    {
        if ($this->type === self::TYPE_FIXED) {
            return number_format($this->value, 0, ',', ' ').' ₽';
        }

        return (int) $this->value.'%';
    }
}
