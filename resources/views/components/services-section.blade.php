@props(['services'])

@php
    $serviceCount = $services->count();
    $gridLayout = $serviceCount === 1
        ? 'sm:max-w-3xl'
        : 'sm:grid-cols-2';
@endphp

<section id="services" class="bg-mist py-16 sm:py-20 lg:py-24" aria-labelledby="services-heading" data-nav-section="services">
    <x-container>
        <div class="max-w-3xl" data-reveal>
            <div class="flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-steel">
                <span class="h-px w-8" style="background-color: var(--brand-primary)"></span>
                {{ __('site.our_services') }}
            </div>
            <h2 id="services-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.035em] text-ink sm:text-4xl lg:text-[2.75rem]">
                {{ __('site.services_heading') }}
            </h2>
            <p class="mt-4 max-w-2xl text-base leading-7 text-steel">
                {{ __('site.services_description') }}
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 {{ $gridLayout }}">
            @foreach ($services as $service)
                @php
                    $centerLastCard = $serviceCount > 1 && $serviceCount % 2 === 1 && $loop->last;
                @endphp

                <article @class([
                    'motion-card group grid min-w-0 overflow-hidden rounded-sm border border-line bg-white transition duration-300 hover:border-slate-400 hover:shadow-[0_14px_34px_rgba(16,18,20,0.07)] sm:grid-cols-[8.5rem_minmax(0,1fr)] lg:grid-cols-[11rem_minmax(0,1fr)]',
                    'sm:col-span-2 sm:w-[calc(50%-0.625rem)] sm:justify-self-center' => $centerLastCard,
                ]) data-reveal style="--reveal-delay: {{ min($loop->index * 60, 300) }}ms">
                    @if ($service->image_url)
                        <div class="aspect-[16/9] overflow-hidden bg-mist sm:aspect-auto sm:h-full sm:min-h-44">
                            <img
                                src="{{ $service->image_url }}"
                                alt="{{ $service->localized_name }}"
                                class="h-full w-full object-contain object-center p-3 transition duration-500 group-hover:scale-[1.025] sm:p-4"
                                loading="lazy"
                            >
                        </div>
                    @else
                        <div class="relative aspect-[16/9] overflow-hidden bg-[#1a1e21] sm:aspect-auto sm:h-full sm:min-h-44" aria-hidden="true">
                            <div class="absolute inset-0 opacity-35 [background-image:linear-gradient(135deg,transparent_48%,rgba(255,255,255,0.08)_49%,rgba(255,255,255,0.08)_51%,transparent_52%)] [background-size:28px_28px]"></div>
                            <div class="absolute bottom-0 start-0 h-1 w-14" style="background-color: var(--brand-primary)"></div>
                        </div>
                    @endif

                    <div class="flex min-w-0 flex-col justify-center p-5 sm:p-6">
                        <h3 class="break-words font-display text-xl font-extrabold leading-tight tracking-[-0.025em] text-ink">
                            {{ $service->localized_name }}
                        </h3>
                        <p class="mt-3 line-clamp-3 text-sm leading-6 text-steel">
                            {{ $service->localized_short_description }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </x-container>
</section>
