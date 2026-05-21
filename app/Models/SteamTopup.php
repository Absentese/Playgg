<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SteamTopup extends Model
{
    public const STATUSES = [
        'pending' => 'Ожидает оплаты',
        'processing' => 'Зачисление',
        'completed' => 'Зачислено',
        'cancelled' => 'Отменено',
    ];

    protected $fillable = [
        'user_id',
        'steam_id',
        'amount',
        'email',
        'status',
        'paid',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function formattedSteamId(): string
    {
        $digits = preg_replace('/\D/', '', $this->steam_id) ?? '';

        if (strlen($digits) !== 17) {
            return $this->steam_id;
        }

        return substr($digits, 0, 3).'-'
            .substr($digits, 3, 3).'-'
            .substr($digits, 6, 3).'-'
            .substr($digits, 9, 4).'-'
            .substr($digits, 13, 4);
    }
}
