<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoMetadata
{
    public function __construct(private readonly BusinessSettings $settings)
    {
    }

    public function homepage(): array
    {
        $businessName = $this->businessName();
        $title = $this->settings->localized('seo_title')
            ?: $businessName.' | '.($this->settings->localized('hero_heading') ?: __('site.seo_home_fallback'));
        $description = $this->description(
            $this->settings->localized('seo_description')
                ?: $this->settings->localized('hero_description')
                ?: __('site.seo_home_description', ['business' => $businessName]),
        );
        $canonical = url('/');
        $image = $this->businessImage('hero_image') ?? $this->businessImage('logo');

        return $this->base($title, $description, $canonical, 'website', $image, [
            '@context' => 'https://schema.org',
            '@type' => 'AutoRepair',
            'name' => $businessName,
            'url' => $canonical,
            'logo' => $this->businessImage('logo'),
            'telephone' => $this->settings->phone_number,
            'address' => $this->settings->address,
            'sameAs' => $this->socialLinks(),
        ]);
    }

    public function catalog(): array
    {
        $businessName = $this->businessName();

        return $this->base(
            __('site.seo_products_title', ['business' => $businessName]),
            $this->description(__('site.catalog_description')),
            route('products.index'),
            'website',
            $this->businessImage('logo'),
        );
    }

    public function product(Product $product): array
    {
        $businessName = $this->businessName();
        $description = $product->localized_short_description
            ?: $product->localized_description
            ?: __('site.seo_product_description', ['product' => $product->localized_name, 'business' => $businessName]);
        $image = $product->main_image && Storage::disk('public')->exists($product->main_image)
            ? $this->absoluteUrl(Storage::disk('public')->url($product->main_image))
            : $this->businessImage('logo');

        return $this->base(
            $product->localized_name.' | '.$businessName,
            $this->description($description),
            route('products.show', $product),
            'product',
            $image,
        );
    }

    private function base(
        string $title,
        string $description,
        string $canonical,
        string $type,
        ?string $image = null,
        ?array $structuredData = null,
    ): array {
        return array_filter([
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'type' => $type,
            'image' => $image,
            'site_name' => $this->businessName(),
            'locale' => app()->isLocale('ar') ? 'ar_AR' : 'en_US',
            'structured_data' => $structuredData ? array_filter(
                $structuredData,
                static fn (mixed $value): bool => filled($value),
            ) : null,
        ], static fn (mixed $value): bool => filled($value));
    }

    private function description(?string $value): string
    {
        $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');

        return Str::limit($plainText, 160, '');
    }

    private function businessName(): string
    {
        return (string) ($this->settings->business_name ?: config('app.name'));
    }

    private function businessImage(string $key): ?string
    {
        $path = $this->settings->get($key);

        if (! is_string($path) || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'images/')) {
            return is_file(public_path($path)) ? asset($path) : null;
        }

        return Storage::disk('public')->exists($path)
            ? $this->absoluteUrl(Storage::disk('public')->url($path))
            : null;
    }

    private function absoluteUrl(string $url): string
    {
        return Str::startsWith($url, ['http://', 'https://']) ? $url : url($url);
    }

    private function socialLinks(): array
    {
        return collect([
            $this->settings->facebook_url,
            $this->settings->instagram_url,
            $this->settings->tiktok_url,
        ])->filter(fn (mixed $url): bool => is_string($url) && in_array(
            strtolower((string) parse_url($url, PHP_URL_SCHEME)),
            ['http', 'https'],
            true,
        ))->values()->all();
    }
}
