<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'image'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function coverProduct(): ?Product
    {
        if ($this->relationLoaded('products') && $this->products->isNotEmpty()) {
            return $this->products->first();
        }

        return $this->products()
            ->where('available', true)
            ->whereNotNull('image')
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->first();
    }

    public function coverImageUrl(): string
    {
        return $this->coverProduct()?->imageUrl() ?? asset('images/game-placeholder.svg');
    }
}
