<x-layouts.app :seo="app(\App\Support\SeoMetadata::class)->homepage()">
    <x-hero />

    @if ($services->isNotEmpty())
        <x-services-section :services="$services" />
    @endif

    @if ($productCategories->isNotEmpty())
        <x-product-categories-section :categories="$productCategories" />
    @endif

    <x-about-section />
    <x-why-choose-us-section />

    @if ($galleryItems->isNotEmpty())
        <x-gallery-section :items="$galleryItems" />
    @endif

    <x-contact-section />
</x-layouts.app>
