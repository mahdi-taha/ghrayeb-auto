<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (Service $service): void {
            foreach (['name', 'short_description', 'description'] as $field) {
                $englishField = "{$field}_en";

                if (array_key_exists($englishField, $service->getAttributes())) {
                    $service->setAttribute($field, $service->getAttribute($englishField));
                }
            }
        });
    }

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
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function getLocalizedNameAttribute(): string
    {
        return $this->localizedValue('name');
    }

    public function getLocalizedShortDescriptionAttribute(): string
    {
        return $this->localizedValue('short_description');
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description') ?: null;
    }

    private function localizedValue(string $key): string
    {
        $locale = app()->getLocale();

        return (string) ($this->getAttribute("{$key}_{$locale}")
            ?: $this->getAttribute("{$key}_en")
            ?: $this->getAttribute($key)
            ?: '');
    }
}
