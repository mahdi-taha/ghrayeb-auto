@php
    $locale = app()->getLocale();
    $points = collect($businessSettings->about_points ?? [])
        ->map(fn (array $point): array => [
            'title' => $point["title_{$locale}"] ?? $point['title_en'] ?? '',
            'description' => $point["description_{$locale}"] ?? $point['description_en'] ?? '',
        ])
        ->filter(fn (array $point): bool => filled($point['title']))
        ->values();
@endphp

@if ($points->isNotEmpty())
    <section @if(! $homepageSections['about_primary']) id="about" @endif class="why-section bg-white py-20 sm:py-24 lg:py-28" aria-labelledby="why-heading" data-nav-section="about">
        <x-container>
            <div class="flex min-w-0 items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-steel" data-reveal>
                <span class="h-px w-8 shrink-0 bg-[var(--brand-primary)]"></span>
                <h2 id="why-heading" class="[overflow-wrap:anywhere]">{{ __('site.why_choose_us') }}</h2>
            </div>

            <ol class="why-points mt-9 border-y border-[#121416]/15 sm:mt-11">
                @foreach ($points as $point)
                    <li class="why-point min-w-0 py-7 sm:py-8" data-reveal style="--reveal-delay: {{ min($loop->index * 60, 300) }}ms">
                        <div class="text-xs font-extrabold tracking-[0.16em] text-[var(--brand-primary)]">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <h3 class="mt-3 max-w-full font-display text-xl font-extrabold leading-snug text-[#121416] [overflow-wrap:anywhere] sm:text-2xl">{{ $point['title'] }}</h3>
                        @if ($point['description'])
                            <p class="mt-3 max-w-full text-sm leading-6 text-[#65696f] [overflow-wrap:anywhere]">{{ $point['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </x-container>
    </section>
@endif
