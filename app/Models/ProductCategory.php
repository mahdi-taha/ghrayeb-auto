<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductCategory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInHomepageOrder(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->localizedValue('name');
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description') ?: null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    private function localizedValue(string $key): string
    {
        return (string) ($this->getAttribute($key.'_'.app()->getLocale())
            ?: $this->getAttribute($key.'_en')
            ?: '');
    }
}
