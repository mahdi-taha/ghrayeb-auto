<?php

namespace App\Support;

use App\Models\GalleryItem;
use App\Models\Service;

class HomepageSections
{
    private ?array $visibility = null;

    public function visibility(): array
    {
        return $this->visibility ??= $this->resolve();
    }

    private function resolve(): array
    {
        $settings = app(BusinessSettings::class);
        $aboutHasContent = filled($settings->localized('about_heading'))
            || filled($settings->localized('about_description'))
            || filled($settings->assetUrl('about_image'));
        $aboutSectionVisible = (bool) $settings->show_about_section && $aboutHasContent;
        $locale = app()->getLocale();
        $whySectionVisible = collect($settings->about_points ?? [])->contains(
            fn (array $point): bool => filled($point["title_{$locale}"] ?? $point['title_en'] ?? null),
        );

        return [
            'services' => Service::query()->active()->exists(),
            'gallery' => GalleryItem::query()->active()->exists(),
            'about' => $aboutSectionVisible || $whySectionVisible,
            'about_primary' => $aboutSectionVisible,
            'contact' => (bool) $settings->get('show_contact_section', true),
        ];
    }
}
