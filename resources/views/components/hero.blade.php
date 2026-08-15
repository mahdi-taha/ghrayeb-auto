@php
    $heading = $businessSettings->localized('hero_heading', __('site.hero_heading'));
    $description = $businessSettings->localized('hero_description', __('site.hero_description'));
    $heroImage = $businessSettings->assetUrl('hero_image');
    $whatsapp = $businessSettings->whatsapp_number;
    $phone = $businessSettings->phone_number;
    $primaryUrl = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : '#contact';
    $primaryLabel = $businessSettings->localized('primary_cta_text', $whatsapp ? __('site.message_whatsapp') : __('site.contact_us'));
    $secondaryLabel = $businessSettings->localized('secondary_cta_text', __('site.view_our_work'));
@endphp

<section id="top" class="hero-section" data-nav-section="home" @if($heroImage) style="--hero-image: url('{{ $heroImage }}')" @endif>
    <div class="hero-section__media" aria-hidden="true"></div>
    <div class="hero-section__overlay" aria-hidden="true"></div>

    <x-container fluid class="relative z-10 flex min-h-[39rem] items-center py-14 sm:min-h-[42rem] sm:py-16 lg:min-h-[44rem]">
        <div class="hero-section__content max-w-[39rem]">
            <div class="mb-5 flex items-center gap-3 text-xs font-bold uppercase tracking-[0.16em] text-white/65 sm:text-sm" data-reveal style="--reveal-delay: 0ms">
                <span class="h-0.5 w-8" style="background: var(--brand-primary)"></span>
                {{ __('site.hero_eyebrow') }}
            </div>

            <h1 class="font-display max-w-[14ch] text-[clamp(2.5rem,5vw,4.25rem)] font-extrabold leading-[1.04] tracking-[-0.04em] text-white" data-reveal style="--reveal-delay: 70ms">
                {{ $heading }}
            </h1>

            <p class="mt-6 max-w-[36rem] text-base leading-7 text-slate-300 sm:text-lg sm:leading-8" data-reveal style="--reveal-delay: 140ms">
                {{ $description }}
            </p>

            <div class="mt-8 flex flex-col gap-3 min-[420px]:flex-row sm:mt-9" data-reveal style="--reveal-delay: 210ms">
                <a href="{{ $primaryUrl }}" @if($whatsapp) target="_blank" rel="noopener noreferrer" @endif class="button-primary">
                    {{ $primaryLabel }}
                    <svg aria-hidden="true" class="directional-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="#our-work" class="button-secondary button-secondary--dark">
                    {{ $secondaryLabel }}
                    <svg aria-hidden="true" class="directional-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            @if ($phone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-white transition-colors hover:text-white/70">
                    <svg aria-hidden="true" class="size-4" style="color: var(--brand-primary)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"/></svg>
                    {{ __('site.call_number', ['phone' => $phone]) }}
                </a>
            @endif
        </div>

    </x-container>
</section>
