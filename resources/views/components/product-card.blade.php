@props(['product'])

@php
    $formattedPrice = number_format((float) $product->price, 2);
    $currency = trim((string) $businessSettings->currency_code);
    $currencyIsSymbol = $currency !== '' && (! ctype_alpha($currency) || mb_strlen($currency) === 1);
    $priceLabel = $currency === ''
        ? $formattedPrice
        : ($currencyIsSymbol ? $currency.$formattedPrice : $formattedPrice.' '.$currency);
@endphp

<article class="motion-card group flex min-w-0 flex-col overflow-hidden rounded-sm border border-line bg-white transition duration-300 hover:border-slate-400 hover:shadow-[0_14px_34px_rgba(16,18,20,0.07)]">
    <a href="{{ route('products.show', $product) }}" class="flex h-full min-w-0 flex-col focus:outline-none">
        <div class="aspect-[3/2] shrink-0 overflow-hidden bg-mist">
            @if ($product->main_image_url)
                <img src="{{ $product->main_image_url }}" alt="{{ $product->localized_name }}" class="h-full w-full object-contain object-center p-3 transition duration-500 group-hover:scale-[1.025] sm:p-4" loading="lazy">
            @else
                <div class="relative h-full w-full" aria-hidden="true">
                    <div class="absolute inset-0 opacity-35 [background-image:linear-gradient(135deg,transparent_48%,rgba(255,255,255,0.08)_49%,rgba(255,255,255,0.08)_51%,transparent_52%)] [background-size:28px_28px]"></div>
                </div>
            @endif
        </div>
        <div class="flex min-w-0 flex-1 flex-col p-4">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-steel"><bdi dir="auto">{{ $product->category->localized_name }}</bdi></p>
            <h2 class="mt-2 min-h-[3rem] break-words font-display text-xl font-extrabold leading-tight tracking-[-0.025em] text-ink"><bdi dir="auto" class="line-clamp-2">{{ $product->localized_name }}</bdi></h2>
            <div class="mt-2.5 min-h-10">
                @if ($product->localized_short_description)
                    <p class="line-clamp-2 text-sm leading-5 text-steel" dir="auto">{{ $product->localized_short_description }}</p>
                @endif
            </div>
            <div class="mt-auto flex min-h-12 flex-wrap items-center justify-between gap-3 border-t border-line pt-3.5">
                @if ($product->show_price && $product->price !== null)
                    <bdi dir="ltr" class="font-extrabold text-ink">{{ $priceLabel }}</bdi>
                @else
                    <span class="text-xs font-bold uppercase tracking-[0.12em] {{ $product->is_available ? 'text-emerald-700' : 'text-steel' }}">
                        {{ $product->is_available ? __('site.available') : __('site.unavailable') }}
                    </span>
                @endif
                <span class="product-action inline-flex items-center gap-2 text-sm font-bold text-ink">
                    {{ __('site.view_product') }}
                    <svg aria-hidden="true" class="directional-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            </div>
        </div>
    </a>
</article>
