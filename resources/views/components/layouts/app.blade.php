@props(['title' => null, 'metaDescription' => null, 'seo' => []])

@php
    $pageTitle = $seo['title'] ?? $title ?? $businessSettings->business_name;
    $pageDescription = $seo['description'] ?? $metaDescription;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="{{ $businessSettings->primary_color }}">

        <title>{{ $pageTitle }}</title>
        @if ($pageDescription)
            <meta name="description" content="{{ $pageDescription }}">
        @endif

        @if ($seo['canonical'] ?? null)
            <link rel="canonical" href="{{ $seo['canonical'] }}">
        @endif

        @if ($seo)
            <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
            <meta property="og:title" content="{{ $pageTitle }}">
            @if ($pageDescription)<meta property="og:description" content="{{ $pageDescription }}">@endif
            <meta property="og:url" content="{{ $seo['canonical'] }}">
            <meta property="og:site_name" content="{{ $seo['site_name'] }}">
            <meta property="og:locale" content="{{ $seo['locale'] }}">
            @if ($seo['image'] ?? null)<meta property="og:image" content="{{ $seo['image'] }}">@endif

            <meta name="twitter:card" content="{{ ($seo['image'] ?? null) ? 'summary_large_image' : 'summary' }}">
            <meta name="twitter:title" content="{{ $pageTitle }}">
            @if ($pageDescription)<meta name="twitter:description" content="{{ $pageDescription }}">@endif
            @if ($seo['image'] ?? null)<meta name="twitter:image" content="{{ $seo['image'] }}">@endif

            @if ($seo['structured_data'] ?? null)
                <script type="application/ld+json">{!! json_encode($seo['structured_data'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
            @endif
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body style="--brand-primary: {{ $businessSettings->primary_color }}; --brand-secondary: {{ $businessSettings->secondary_color }};">
        <a href="#main-content" class="skip-link">{{ __('site.skip_to_content') }}</a>

        <x-site-header />

        <main id="main-content">
            {{ $slot }}
        </main>

        <x-site-footer />
    </body>
</html>
