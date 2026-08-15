<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'specifications' => 'array',
            'price' => 'decimal:2',
            'show_price' => 'boolean',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->where('is_active', true));
    }

    public function scopeInCatalogOrder(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->localizedValue('name');
    }

    public function getLocalizedShortDescriptionAttribute(): ?string
    {
        return $this->localizedValue('short_description') ?: null;
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description') ?: null;
    }

    public function getLocalizedSpecificationsAttribute(): array
    {
        $locale = app()->getLocale();

        return collect($this->specifications ?? [])
            ->map(fn (array $specification): array => [
                'label' => $specification["label_{$locale}"] ?? $specification['label_en'] ?? '',
                'value' => $specification["value_{$locale}"] ?? $specification['value_en'] ?? '',
            ])
            ->filter(fn (array $specification): bool => filled($specification['label']) && filled($specification['value']))
            ->values()
            ->all();
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->main_image ? Storage::disk('public')->url($this->main_image) : null;
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->all();
    }

    private function localizedValue(string $key): string
    {
        return (string) ($this->getAttribute($key.'_'.app()->getLocale())
            ?: $this->getAttribute($key.'_en')
            ?: '');
    }
}
