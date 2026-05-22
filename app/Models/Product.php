<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'price', 'old_price', 'platform',
        'genre', 'description', 'image', 'available', 'is_featured', 'is_preorder',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'available' => 'boolean',
            'is_featured' => 'boolean',
            'is_preorder' => 'boolean',
        ];
    }

    /**
     * Регистронезависимый поиск по названию, slug и жанру.
     * Одна буква — только начало названия; длиннее — по словам в названии и частям slug (без ложных «of»).
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $lower = mb_strtolower($term, 'UTF-8');
        $length = mb_strlen($lower);

        return $query->where(function (Builder $q) use ($lower, $length) {
            if ($length === 1) {
                $q->whereRaw('LOWER(name) LIKE ?', [$lower.'%']);

                return;
            }

            $q->where(function (Builder $nameQuery) use ($lower) {
                $nameQuery->whereRaw('LOWER(name) LIKE ?', [$lower.'%']);

                foreach ([' ', ': ', ' — ', '-', '–'] as $separator) {
                    $nameQuery->orWhereRaw('LOWER(name) LIKE ?', ['%'.$separator.$lower.'%']);
                }
            });

            $q->orWhereRaw('LOWER(slug) LIKE ?', [$lower.'-%'])
                ->orWhereRaw('LOWER(slug) LIKE ?', ['%-'.$lower.'-%'])
                ->orWhereRaw('LOWER(slug) LIKE ?', ['%-'.$lower])
                ->orWhereRaw('LOWER(slug) = ?', [$lower]);

            $q->orWhereRaw('LOWER(genre) LIKE ?', ['%'.$lower.'%']);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discountPercent(): ?int
    {
        if (! $this->old_price || $this->old_price <= $this->price) {
            return null;
        }

        return (int) round((1 - ($this->price / $this->old_price)) * 100);
    }

    public function hasDiscount(): bool
    {
        return $this->discountPercent() !== null;
    }

    /**
     * @return array<int, array{label: string, url: string|null}>
     */
    public function displayTags(): array
    {
        if (! $this->category) {
            return [];
        }

        return [
            [
                'label' => $this->category->name,
                'url' => route('products', ['category' => $this->category->slug]),
            ],
        ];
    }

    public function hasImageFile(): bool
    {
        if (! $this->image) {
            return false;
        }

        return is_file(public_path('images/products/'.$this->image))
            || is_file(public_path('storage/'.$this->image));
    }

    /**
     * Обложки Steam и загруженные PNG/JPG лучше показывать с contain на тёмном фоне.
     */
    public function hasWhiteMatteBackground(): bool
    {
        if (! $this->hasImageFile()) {
            return false;
        }

        $ext = strtolower(pathinfo((string) $this->image, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            if (file_exists(public_path('images/products/'.$this->image))) {
                return asset('images/products/'.$this->image);
            }

            if (file_exists(public_path('storage/'.$this->image))) {
                return asset('storage/'.$this->image);
            }
        }

        return asset('images/game-placeholder.svg');
    }
}
