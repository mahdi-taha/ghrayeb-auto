<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInHomepageOrder(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image);
    }

    public function getLocalizedTitleAttribute(): ?string
    {
        return $this->localizedValue('title');
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description');
    }

    private function localizedValue(string $key): ?string
    {
        return $this->getAttribute($key.'_'.app()->getLocale())
            ?: $this->getAttribute($key.'_en');
    }
}
