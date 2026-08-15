@php
    $eyebrow = $businessSettings->localized('about_eyebrow') ?: __('site.about_us');
    $headingContent = $businessSettings->localized('about_heading');
    $description = $businessSettings->localized('about_description');
    $imageUrl = $businessSettings->assetUrl('about_image');
    $hasContent = filled($headingContent) || filled($description) || $imageUrl;
    $heading = $headingContent ?: $businessSettings->business_name;
@endphp

@if ($businessSettings->show_about_section && $hasContent)
    <section id="about" class="about-section" aria-labelledby="about-heading" data-nav-section="about">
        <div class="about-section__highlight" aria-hidden="true"></div>

        <x-container class="relative z-10 py-18 sm:py-22 lg:py-28">
            <div class="grid min-w-0 items-center gap-12 lg:grid-cols-[minmax(0,1.12fr)_minmax(18rem,0.88fr)] lg:gap-14 xl:grid-cols-[minmax(0,1.12fr)_minmax(22rem,0.88fr)] xl:gap-16">
                <div class="about-copy min-w-0 max-w-full lg:pe-4" data-reveal>
                    @if ($eyebrow)
                        <div class="flex min-w-0 max-w-full items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-white/75">
                            <span class="h-px w-9 shrink-0 bg-white/70"></span>
                            <span class="min-w-0 max-w-full [overflow-wrap:anywhere]">{{ $eyebrow }}</span>
                        </div>
                    @endif

                    @if ($heading)
                        <h2 id="about-heading" class="mt-5 max-w-[min(16ch,100%)] font-display text-[clamp(2.35rem,5vw,4.75rem)] font-extrabold leading-[1.02] tracking-[-0.045em] text-white [overflow-wrap:anywhere]">
                            {{ $heading }}
                        </h2>
                    @endif

                    @if ($description)
                        <p class="mt-6 max-w-full text-base leading-7 text-white/85 [overflow-wrap:anywhere] sm:max-w-2xl sm:text-lg sm:leading-8">
                            {{ $description }}
                        </p>
                    @endif
                </div>

                <div class="about-visual">
                    <div class="about-visual__frame">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $heading ?: $businessSettings->business_name }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="about-visual__fallback" aria-hidden="true"></div>
                        @endif
                    </div>
                </div>
            </div>
        </x-container>
    </section>
@endif
