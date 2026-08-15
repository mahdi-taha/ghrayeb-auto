@php
    $phone = trim((string) $businessSettings->phone_number);
    $whatsapp = trim((string) $businessSettings->whatsapp_number);
    $email = trim((string) $businessSettings->email);
    $address = trim((string) $businessSettings->address);
    $mapsUrl = trim((string) $businessSettings->google_maps_url);
    $mapsEmbedUrl = trim((string) $businessSettings->google_maps_embed_url);
    $hours = collect(preg_split('/\R/u', trim((string) $businessSettings->opening_hours)))
        ->map(fn ($line) => trim($line))
        ->filter();
    $phoneValue = preg_replace('/[^\d+]/', '', $phone);
    $whatsappValue = preg_replace('/\D+/', '', $whatsapp);
    $phoneHref = $phoneValue ? 'tel:' . $phoneValue : null;
    $whatsappHref = $whatsappValue ? 'https://wa.me/' . $whatsappValue : null;
    $mapParts = $mapsUrl ? parse_url($mapsUrl) : false;
    $mapScheme = is_array($mapParts) ? strtolower($mapParts['scheme'] ?? '') : '';
    $safeMapsUrl = in_array($mapScheme, ['http', 'https'], true) ? $mapsUrl : null;
    $embedParts = $mapsEmbedUrl ? parse_url($mapsEmbedUrl) : false;
    $embedHost = is_array($embedParts) ? strtolower($embedParts['host'] ?? '') : '';
    $embedPath = is_array($embedParts) ? ($embedParts['path'] ?? '') : '';
    $embedScheme = is_array($embedParts) ? strtolower($embedParts['scheme'] ?? '') : '';
    $isGoogleEmbedHost = $embedHost === 'google.com' || str_ends_with($embedHost, '.google.com');
    $embedUrl = in_array($embedScheme, ['https'], true) && $isGoogleEmbedHost && str_starts_with($embedPath, '/maps/embed') ? $mapsEmbedUrl : null;
    $eyebrow = $businessSettings->localized('contact_eyebrow', __('site.contact_eyebrow'));
    $heading = $businessSettings->localized('contact_heading', __('site.contact_heading'));
    $description = $businessSettings->localized('contact_description', __('site.contact_description'));
    $hasDetails = $phone || $whatsapp || $email || $address || $hours->isNotEmpty() || $safeMapsUrl;
@endphp

@if ($businessSettings->get('show_contact_section', true) && ($hasDetails || $eyebrow || $heading || $description))
    <section id="contact" class="contact-section relative isolate overflow-hidden bg-[#141517] py-16 text-white sm:py-20 lg:py-24" aria-labelledby="contact-heading" data-nav-section="contact">
        <div class="contact-section__glow" aria-hidden="true"></div>
        <x-container class="relative z-10">
            <div class="grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] lg:items-center lg:gap-12 xl:gap-16">
                <div class="min-w-0 py-2 lg:py-5" data-reveal>
                    @if ($eyebrow)
                        <div class="flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-white/60">
                            <span class="h-px w-8 bg-[var(--brand-primary)]"></span>{{ $eyebrow }}
                        </div>
                    @endif
                    @if ($heading)
                        <h2 id="contact-heading" class="mt-4 max-w-[16ch] break-words font-display text-3xl font-extrabold tracking-[-0.04em] sm:text-4xl lg:text-5xl">{{ $heading }}</h2>
                    @endif
                    @if ($description)
                        <p class="mt-5 max-w-xl break-words text-base leading-7 text-white/68">{{ $description }}</p>
                    @endif

                    @if ($phone || $email || $address)
                        <address class="contact-details mt-9 grid min-w-0 gap-x-8 gap-y-7 not-italic sm:grid-cols-2 sm:gap-y-8">
                            @if ($phone)
                                <a href="{{ $phoneHref }}" class="contact-detail focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white"><span>{{ __('site.phone') }}</span><strong dir="ltr">{{ $phone }}</strong></a>
                            @endif
                            @if ($email)
                                <a href="mailto:{{ $email }}" class="contact-detail focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white"><span>{{ __('site.email') }}</span><strong class="break-all">{{ $email }}</strong></a>
                            @endif
                            @if ($address)
                                <div class="contact-detail sm:col-span-2 sm:pt-1"><span>{{ __('site.location') }}</span><strong class="whitespace-pre-line break-words">{{ $address }}</strong></div>
                            @endif
                        </address>
                    @endif

                    @if ($hours->isNotEmpty())
                        <div class="mt-9 border-s-2 border-[var(--brand-primary)] ps-5">
                            <h3 class="text-xs font-extrabold uppercase tracking-[0.18em] text-white/55">{{ __('site.business_hours') }}</h3>
                            <div class="mt-3 space-y-1.5 text-sm leading-6 text-white/85">
                                @foreach ($hours as $line)<p class="break-words">{{ $line }}</p>@endforeach
                            </div>
                        </div>
                    @endif

                    @if ($phoneHref || $whatsappHref)
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            @if ($phoneHref)<a href="{{ $phoneHref }}" class="button-primary justify-center">{{ __('site.call_now') }}</a>@endif
                            @if ($whatsappHref)<a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" class="button-secondary border-white/25 bg-transparent text-white hover:border-white hover:bg-white hover:text-ink">{{ __('site.whatsapp') }}</a>@endif
                        </div>
                    @endif
                </div>

                <div class="contact-map relative h-[17rem] w-full overflow-hidden bg-[#1b1d20] sm:h-[22rem] lg:h-[24rem]">
                    @if ($embedUrl)
                        <iframe src="{{ $embedUrl }}" class="absolute inset-0 h-full w-full border-0 grayscale-[0.15]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('site.map_title') }}"></iframe>
                    @else
                        <div class="contact-map__fallback absolute inset-0" aria-hidden="true"></div>
                        <div class="relative flex h-full flex-col items-start justify-center p-6 sm:p-8 lg:p-9">
                            <svg aria-hidden="true" class="mb-6 size-8 text-[var(--brand-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-white/48">{{ __('site.find_us') }}</p>
                            @if ($address)<p class="mt-3 max-w-lg whitespace-pre-line break-words font-display text-3xl font-extrabold leading-tight sm:text-4xl">{{ $address }}</p>@endif
                            <p class="mt-3 max-w-sm text-sm leading-6 text-white/58">{{ __('site.find_us_description') }}</p>
                            @if ($safeMapsUrl)<a href="{{ $safeMapsUrl }}" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex min-h-11 items-center gap-2 border-b border-[var(--brand-primary)] font-bold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">{{ __('site.get_directions') }} <span class="directional-icon" aria-hidden="true">↗</span></a>@endif
                        </div>
                    @endif
                </div>
            </div>
        </x-container>
    </section>
@endif
