@php
    $logoUrl = $businessSettings->assetUrl('logo');
    $phone = $businessSettings->phone_number;
    $whatsapp = $businessSettings->whatsapp_number;
    $whatsappUrl = $whatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $whatsapp) : null;
    $isProductsRoute = request()->routeIs('products.*');
    $isHomepage = request()->is('/');
    $initialActiveKey = $isProductsRoute ? 'products' : 'home';
    $navigation = array_values(array_filter([
        ['key' => 'home', 'label' => __('site.home'), 'href' => url('/')],
        $homepageSections['services'] ? ['key' => 'services', 'label' => __('site.services'), 'href' => url('/').'#services'] : null,
        ['key' => 'products', 'label' => __('site.products'), 'href' => route('products.index')],
        $homepageSections['gallery'] ? ['key' => 'our-work', 'label' => __('site.our_work'), 'href' => url('/').'#our-work'] : null,
        $homepageSections['about'] ? ['key' => 'about', 'label' => __('site.about'), 'href' => url('/').'#about'] : null,
        $homepageSections['contact'] ? ['key' => 'contact', 'label' => __('site.contact'), 'href' => url('/').'#contact'] : null,
    ]));
@endphp

<header class="sticky top-0 z-50 border-b border-line bg-white/95 shadow-[0_4px_18px_rgba(16,18,20,0.04)] backdrop-blur-md" data-site-header @if($isHomepage) data-homepage-nav @endif data-initial-nav="{{ $initialActiveKey }}">
    <x-container fluid class="flex h-20 items-center justify-between gap-5 lg:h-[5.5rem]">
        <a href="#top" class="flex h-full w-44 shrink-0 items-center sm:w-52 lg:w-60" aria-label="{{ $businessSettings->business_name }} {{ __('site.home') }}">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $businessSettings->business_name }}" class="max-h-[3.75rem] w-full object-contain object-left lg:max-h-[4.5rem]" width="146" height="47">
            @else
                <span class="text-xl font-extrabold tracking-tight">{{ $businessSettings->business_name }}</span>
            @endif
        </a>

        <nav class="hidden items-center gap-5 xl:flex 2xl:gap-7" aria-label="{{ __('site.primary_navigation') }}">
            @foreach ($navigation as $item)
                <a @class(['nav-link', 'is-active' => $item['key'] === $initialActiveKey]) href="{{ $item['href'] }}" data-nav-key="{{ $item['key'] }}" @if($isProductsRoute && $item['key'] === 'products') aria-current="page" @endif>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden shrink-0 items-center gap-3 xl:flex">
            <x-language-switcher />

            @if ($phone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="group flex items-center gap-3 px-2 py-2" aria-label="Call {{ $phone }}">
                    <span class="grid size-9 place-items-center border border-line text-ink transition-colors group-hover:border-red-200 group-hover:text-red-700" style="color: var(--brand-primary)">
                        <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z"/></svg>
                    </span>
                    <span class="hidden xl:block">
                        <span class="block text-[0.65rem] font-bold uppercase tracking-[0.15em] text-steel">{{ __('site.call_us') }}</span>
                        <span class="block text-sm font-extrabold text-ink">{{ $phone }}</span>
                    </span>
                </a>
            @endif

            <a href="{{ $whatsappUrl ?? '#contact' }}" @if($whatsappUrl) target="_blank" rel="noopener noreferrer" @endif class="button-primary min-h-11 px-5">
                {{ __('site.contact_us') }}
                <svg aria-hidden="true" class="directional-icon size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <details class="group relative xl:hidden">
            <summary class="grid size-11 cursor-pointer list-none place-items-center rounded-sm border border-line bg-white text-ink marker:content-none" aria-label="{{ __('site.open_navigation') }}">
                <svg aria-hidden="true" class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg aria-hidden="true" class="hidden size-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18"/></svg>
            </summary>

            <div class="mobile-navigation-panel absolute top-[calc(100%+1rem)] w-[min(19rem,calc(100vw-2.5rem))] rounded-sm border border-line bg-white p-3 shadow-[0_20px_50px_rgba(16,18,20,0.16)]">
                <nav class="flex flex-col" aria-label="{{ __('site.mobile_navigation') }}">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['href'] }}" @class(['mobile-nav-link border-b border-line px-4 py-3.5 text-sm font-extrabold uppercase tracking-[0.08em] last:border-0 hover:bg-mist', 'is-active' => $item['key'] === $initialActiveKey]) data-nav-key="{{ $item['key'] }}" @if($isProductsRoute && $item['key'] === 'products') aria-current="page" @endif>{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="mt-3 grid gap-2">
                    <x-language-switcher mobile />
                    @if ($phone)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="flex min-h-11 items-center justify-center border border-line px-4 text-sm font-bold">{{ __('site.call_number', ['phone' => $phone]) }}</a>
                    @endif
                    <a href="{{ $whatsappUrl ?? '#contact' }}" @if($whatsappUrl) target="_blank" rel="noopener noreferrer" @endif class="button-primary w-full">{{ __('site.contact_us') }}</a>
                </div>
            </div>
        </details>
    </x-container>
</header>
