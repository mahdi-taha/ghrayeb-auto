@php
    $whatsapp = preg_replace('/\D+/', '', (string) $businessSettings->whatsapp_number);
    $phone = preg_replace('/[^\d+]/', '', (string) $businessSettings->phone_number);
    $message = __('site.whatsapp_product_message', ['product' => $product->localized_name]);
    $contactUrl = $whatsapp
        ? 'https://wa.me/'.$whatsapp.'?text='.rawurlencode($message)
        : ($phone ? 'tel:'.$phone : url('/').'#contact');
    $images = collect([$product->main_image_url])->merge($product->gallery_urls)->filter()->unique()->values();
    $primaryImage = $images->first();
    $formattedPrice = number_format((float) $product->price, 2);
    $currency = trim((string) $businessSettings->currency_code);
    $currencyIsSymbol = $currency !== '' && (! ctype_alpha($currency) || mb_strlen($currency) === 1);
    $priceLabel = $currency === '' ? $formattedPrice : ($currencyIsSymbol ? $currency.$formattedPrice : $formattedPrice.' '.$currency);
@endphp

<x-layouts.app :seo="app(\App\Support\SeoMetadata::class)->product($product)">
    <section class="bg-white pb-16 pt-9 sm:pb-20 sm:pt-12 lg:pb-24 lg:pt-14">
        <x-container>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-bold text-steel transition-colors hover:text-ink">
                <svg aria-hidden="true" class="back-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                {{ __('site.back_to_products') }}
            </a>

            <div class="mt-7 grid min-w-0 gap-10 lg:grid-cols-2 lg:gap-14 xl:gap-16">
                <div class="min-w-0" data-product-gallery>
                    <div class="aspect-[4/3] overflow-hidden rounded-sm border border-line bg-mist">
                        @if ($primaryImage)
                            <img src="{{ $primaryImage }}" alt="{{ $product->localized_name }}" class="h-full w-full object-contain p-3 sm:p-5" data-product-main-image>
                        @else
                            <div class="relative h-full w-full bg-[#1a1e21]" aria-label="{{ $product->localized_name }}">
                                <div class="absolute inset-0 opacity-30 [background-image:linear-gradient(135deg,transparent_48%,rgba(255,255,255,0.08)_49%,rgba(255,255,255,0.08)_51%,transparent_52%)] [background-size:28px_28px]"></div>
                            </div>
                        @endif
                    </div>

                    @if ($images->count() > 1)
                        <div class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-5" aria-label="{{ __('site.product_gallery') }}">
                            @foreach ($images as $imageUrl)
                                <button type="button" class="aspect-square overflow-hidden rounded-sm border-2 {{ $loop->first ? 'border-[var(--brand-primary)]' : 'border-line' }} bg-mist transition hover:border-slate-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--brand-primary)]" data-product-thumbnail data-image="{{ $imageUrl }}" aria-label="{{ __('site.show_product_image', ['number' => $loop->iteration]) }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                    <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-contain p-1.5" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="min-w-0 lg:py-3">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-steel"><bdi dir="auto">{{ $product->category->localized_name }}</bdi></p>
                    <h1 class="mt-3 break-words font-display text-4xl font-extrabold leading-tight tracking-[-0.04em] text-ink sm:text-5xl"><bdi dir="auto">{{ $product->localized_name }}</bdi></h1>

                    <div class="mt-6 min-h-10">
                        @if ($product->show_price && $product->price !== null)
                            <bdi dir="ltr" class="text-2xl font-extrabold text-ink sm:text-3xl">{{ $priceLabel }}</bdi>
                        @else
                            <span class="text-sm font-bold {{ $product->is_available ? 'text-emerald-700' : 'text-steel' }}">{{ $product->is_available ? __('site.available') : __('site.unavailable') }}</span>
                        @endif
                    </div>

                    @if ($product->localized_description)
                        <div class="prose prose-slate mt-7 max-w-none leading-7 text-steel" dir="auto">{!! $product->localized_description !!}</div>
                    @elseif ($product->localized_short_description)
                        <p class="mt-7 leading-7 text-steel" dir="auto">{{ $product->localized_short_description }}</p>
                    @endif

                    @if ($product->localized_specifications)
                        <section class="mt-9 border-t border-line pt-7" aria-labelledby="specifications-heading">
                            <h2 id="specifications-heading" class="font-display text-2xl font-extrabold text-ink">{{ __('site.specifications') }}</h2>
                            <dl class="mt-5 border-y border-line">
                                @foreach ($product->localized_specifications as $specification)
                                    <div class="grid min-w-0 grid-cols-[minmax(0,0.75fr)_minmax(0,1.25fr)] gap-5 border-b border-line py-3.5 text-sm last:border-b-0 sm:py-4">
                                        <dt class="break-words font-semibold text-steel" dir="auto">{{ $specification['label'] }}</dt>
                                        <dd class="break-words font-bold text-ink" dir="auto">{{ $specification['value'] }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </section>
                    @endif

                    <a href="{{ $contactUrl }}" @if($whatsapp) target="_blank" rel="noopener noreferrer" @endif class="button-primary mt-9">
                        {{ __('site.contact_about_product') }}
                        <svg aria-hidden="true" class="directional-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </div>
        </x-container>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="border-t border-line bg-mist py-14 sm:py-16 lg:py-20" aria-labelledby="related-products-heading">
            <x-container>
                <h2 id="related-products-heading" class="font-display text-3xl font-extrabold tracking-[-0.035em] text-ink sm:text-4xl">{{ __('site.related_products') }}</h2>
                <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif
</x-layouts.app>
