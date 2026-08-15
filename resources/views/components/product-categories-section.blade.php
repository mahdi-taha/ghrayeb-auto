@props(['categories'])

@php
    $categoryCount = $categories->count();
    $gridLayout = match ($categoryCount) {
        1 => 'max-w-xs',
        2 => 'min-[400px]:grid-cols-2 max-w-xl',
        3 => 'min-[400px]:grid-cols-2 md:grid-cols-3 max-w-4xl',
        4 => 'min-[400px]:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 max-w-5xl',
        default => 'min-[400px]:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6',
    };
@endphp

<section class="bg-white py-14 sm:py-18 lg:py-20" aria-labelledby="product-categories-heading" data-nav-section="products">
    <x-container>
        <div class="max-w-3xl" data-reveal>
            <div class="flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-steel">
                <span class="h-px w-8" style="background-color: var(--brand-primary)"></span>
                {{ __('site.product_range') }}
            </div>
            <h2 id="product-categories-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.035em] text-ink sm:text-4xl lg:text-[2.75rem]">
                {{ __('site.categories_heading') }}
            </h2>
            <p class="mt-4 max-w-2xl text-base leading-7 text-steel">{{ __('site.categories_description') }}</p>
        </div>

        <div class="mx-auto mt-8 grid grid-cols-1 gap-4 {{ $gridLayout }}">
            @foreach ($categories as $category)
                <article class="motion-card group min-w-0 overflow-hidden rounded-sm border border-line bg-white transition duration-300 hover:border-slate-400 hover:shadow-[0_14px_34px_rgba(16,18,20,0.07)]" data-reveal style="--reveal-delay: {{ min($loop->index * 60, 300) }}ms">
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="block focus:outline-none">
                        <div class="aspect-[4/3] overflow-hidden bg-mist">
                            @if ($category->image_url)
                                <img src="{{ $category->image_url }}" alt="{{ $category->localized_name }}" class="h-full w-full object-contain object-center p-3 transition duration-500 group-hover:scale-[1.025] sm:p-4" loading="lazy">
                            @else
                                <div class="relative h-full w-full" aria-hidden="true">
                                    <div class="absolute inset-0 opacity-35 [background-image:linear-gradient(135deg,transparent_48%,rgba(255,255,255,0.08)_49%,rgba(255,255,255,0.08)_51%,transparent_52%)] [background-size:28px_28px]"></div>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="break-words font-display text-base font-extrabold leading-snug tracking-[-0.02em] text-ink sm:text-lg">{{ $category->localized_name }}</h3>
                            @if ($category->localized_description)
                                <p class="mt-1.5 line-clamp-2 text-xs leading-5 text-steel">{{ $category->localized_description }}</p>
                            @endif
                            <span class="category-action mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-ink">
                                {{ __('site.view_products') }}
                                <svg aria-hidden="true" class="directional-icon size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </x-container>
</section>
