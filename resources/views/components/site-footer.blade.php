@php
    $logoUrl = $businessSettings->assetUrl('logo');
    $businessName = $businessSettings->business_name;
    $description = $businessSettings->localized('footer_description');
    $phone = trim((string) $businessSettings->phone_number);
    $whatsapp = trim((string) $businessSettings->whatsapp_number);
    $email = trim((string) $businessSettings->email);
    $address = trim((string) $businessSettings->address);
    $phoneValue = preg_replace('/[^\d+]/', '', $phone);
    $whatsappValue = preg_replace('/\D+/', '', $whatsapp);
    $phoneUrl = $phoneValue ? 'tel:' . $phoneValue : null;
    $whatsappUrl = $whatsappValue ? 'https://wa.me/' . $whatsappValue : null;

    $safeExternalUrl = static function (?string $value): ?string {
        $value = trim((string) $value);
        $scheme = $value ? strtolower((string) parse_url($value, PHP_URL_SCHEME)) : '';

        return in_array($scheme, ['http', 'https'], true) ? $value : null;
    };

    $directionsUrl = $safeExternalUrl($businessSettings->google_maps_url);
    $socials = array_filter([
        ['label' => __('site.facebook'), 'url' => $safeExternalUrl($businessSettings->facebook_url), 'icon' => 'facebook'],
        ['label' => __('site.instagram'), 'url' => $safeExternalUrl($businessSettings->instagram_url), 'icon' => 'instagram'],
        ['label' => __('site.tiktok'), 'url' => $safeExternalUrl($businessSettings->tiktok_url), 'icon' => 'tiktok'],
    ], fn ($social) => filled($social['url']));
    $navigation = array_values(array_filter([
        ['label' => __('site.home'), 'href' => url('/')],
        $homepageSections['services'] ? ['label' => __('site.services'), 'href' => url('/') . '#services'] : null,
        ['label' => __('site.products'), 'href' => route('products.index')],
        $homepageSections['gallery'] ? ['label' => __('site.our_work'), 'href' => url('/') . '#our-work'] : null,
        $homepageSections['about'] ? ['label' => __('site.about'), 'href' => url('/') . '#about'] : null,
        $homepageSections['contact'] ? ['label' => __('site.contact'), 'href' => url('/') . '#contact'] : null,
    ]));
@endphp

<footer class="border-t border-white/10 bg-[#0d0e0f] text-white" aria-label="{{ __('site.footer') }}">
    <x-container>
        <div class="grid gap-10 py-12 sm:py-14 md:grid-cols-2 lg:grid-cols-[minmax(0,1.15fr)_minmax(12rem,0.7fr)_minmax(0,1fr)] lg:gap-12">
            <div class="min-w-0 md:col-span-2 lg:col-span-1">
                @if ($logoUrl)
                    <a href="{{ url('/') }}" class="inline-flex max-w-full rounded-sm bg-white px-4 py-2.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white" aria-label="{{ $businessName }} {{ __('site.home') }}">
                        <img src="{{ $logoUrl }}" alt="{{ $businessName }}" class="h-12 w-auto max-w-[13rem] object-contain sm:h-14 sm:max-w-[15rem]" width="146" height="47">
                    </a>
                @else
                    <a href="{{ url('/') }}" class="font-display text-2xl font-extrabold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">{{ $businessName }}</a>
                @endif

                @if ($description)
                    <p class="mt-5 max-w-md break-words text-sm leading-6 text-white/58">{{ $description }}</p>
                @endif

                @if ($socials)
                    <div class="mt-6 flex flex-wrap gap-2.5">
                        @foreach ($socials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="grid size-11 place-items-center border border-white/15 text-white/70 transition hover:border-[var(--brand-primary)] hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" aria-label="{{ $social['label'] }}">
                                @if ($social['icon'] === 'facebook')
                                    <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8H17V2.4c-.4-.1-1.5-.2-2.8-.2-2.8 0-4.7 1.7-4.7 4.8v2.5H6.4V13h3.1v9h4Z"/></svg>
                                @elseif ($social['icon'] === 'instagram')
                                    <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                @else
                                    <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.7 3c.4 2.2 1.7 3.6 3.8 3.8v3.1a8.7 8.7 0 0 1-3.8-1.1v6.4a6.3 6.3 0 1 1-5.4-6.2v3.2a3.1 3.1 0 1 0 2.2 3V3h3.2Z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <nav aria-label="{{ __('site.footer_navigation') }}">
                <h2 class="text-xs font-extrabold uppercase tracking-[0.18em] text-white/45">{{ __('site.navigation') }}</h2>
                <ul class="mt-5 grid grid-cols-2 gap-x-5 gap-y-1.5 md:grid-cols-1">
                    @foreach ($navigation as $item)
                        <li><a href="{{ $item['href'] }}" class="inline-flex min-h-10 items-center text-sm font-bold text-white/72 transition hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="min-w-0">
                <h2 class="text-xs font-extrabold uppercase tracking-[0.18em] text-white/45">{{ __('site.contact_details') }}</h2>
                <address class="mt-5 space-y-1 not-italic">
                    @if ($phoneUrl)<a href="{{ $phoneUrl }}" class="footer-contact-link"><span>{{ __('site.phone') }}</span><strong dir="ltr">{{ $phone }}</strong></a>@endif
                    @if ($whatsappUrl)<a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="footer-contact-link"><span>{{ __('site.whatsapp') }}</span><strong dir="ltr">{{ $whatsapp }}</strong></a>@endif
                    @if ($email)<a href="mailto:{{ $email }}" class="footer-contact-link"><span>{{ __('site.email') }}</span><strong class="break-all" dir="ltr">{{ $email }}</strong></a>@endif
                    @if ($address)
                        @if ($directionsUrl)<a href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer" class="footer-contact-link"><span>{{ __('site.location') }}</span><strong class="whitespace-pre-line break-words">{{ $address }}</strong></a>
                        @else<div class="footer-contact-link"><span>{{ __('site.location') }}</span><strong class="whitespace-pre-line break-words">{{ $address }}</strong></div>@endif
                    @endif
                </address>
            </div>
        </div>

        <div class="flex flex-col gap-4 border-t border-white/10 py-5 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $businessName }}. {{ __('site.all_rights_reserved') }}</p>
            <x-language-switcher dark />
        </div>
    </x-container>
</footer>
