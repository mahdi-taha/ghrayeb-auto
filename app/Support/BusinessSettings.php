<?php

namespace App\Support;

use App\Models\BusinessSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BusinessSettings
{
    private const CACHE_KEY = 'business.settings';

    public function all(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn (): array => $this->load());
        } catch (QueryException) {
            return $this->load();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function assetUrl(string $key): ?string
    {
        $path = $this->get($key);

        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function localized(string $key, mixed $default = null): mixed
    {
        return $this->get($key.'_'.app()->getLocale())
            ?: $this->get($key.'_en')
            ?: $this->get($key)
            ?: $default;
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    private function load(): array
    {
        try {
            $stored = BusinessSetting::query()->first()?->attributesToArray() ?? [];
        } catch (QueryException) {
            $stored = [];
        }

        return array_replace(config('business.defaults', []), array_filter(
            $stored,
            static fn (mixed $value, string $key): bool => ! in_array($key, ['id', 'created_at', 'updated_at'], true) && $value !== null,
            ARRAY_FILTER_USE_BOTH,
        ));
    }
}
