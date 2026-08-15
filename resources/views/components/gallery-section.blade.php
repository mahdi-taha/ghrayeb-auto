@props(['items'])

<section id="our-work" class="border-t border-[#121416]/[0.06] bg-[#f2f3f3] pb-16 pt-20 sm:pb-20 sm:pt-24 lg:pb-24 lg:pt-28" aria-labelledby="gallery-heading" data-nav-section="our-work">
    <x-container>
        <div class="grid max-w-5xl items-end gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)] lg:gap-7" data-reveal>
            <div class="min-w-0">
                <div class="flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.2em] text-steel">
                    <span class="h-px w-8" style="background-color: var(--brand-primary)"></span>
                    {{ __('site.our_work') }}
                </div>
                <h2 id="gallery-heading" class="mt-4 max-w-[18ch] break-words font-display text-3xl font-extrabold tracking-[-0.04em] text-ink sm:text-4xl lg:text-5xl">
                    {{ __('site.gallery_heading') }}
                </h2>
            </div>
            <p class="max-w-lg text-base leading-7 text-steel lg:border-s lg:border-line lg:ps-7">{{ __('site.gallery_description') }}</p>
        </div>

        <div class="gallery-mosaic gallery-mosaic--count-{{ min($items->count(), 8) }} mt-10" dir="ltr">
            @foreach ($items as $item)
                @php($hasCaption = filled($item->localized_title) || filled($item->localized_description))

                <button
                    type="button"
                    class="gallery-tile group relative min-h-0 min-w-0 cursor-zoom-in overflow-hidden bg-[#17191c] text-start focus:outline-none"
                    data-gallery-trigger
                    data-gallery-image="{{ $item->image_url }}"
                    data-gallery-title="{{ $item->localized_title }}"
                    data-gallery-description="{{ $item->localized_description }}"
                    aria-label="{{ $item->localized_title ?: __('site.view_gallery_image') }}"
                    data-reveal
                    style="--reveal-delay: {{ min($loop->index * 50, 250) }}ms"
                >
                    <img src="{{ $item->image_url }}" alt="{{ $item->localized_title ?: __('site.gallery_image') }}" class="h-full w-full object-cover object-center transition duration-500 group-hover:scale-[1.035] group-focus-visible:scale-[1.035]" loading="lazy">

                    @if ($hasCaption)
                        <span class="gallery-caption absolute inset-x-0 bottom-0 block bg-gradient-to-t from-black/85 via-black/45 to-transparent p-4 pt-12 text-white transition-opacity duration-300 lg:p-5 lg:pt-16">
                            @if ($item->localized_title)
                                <span class="block break-words font-display text-lg font-extrabold leading-snug">{{ $item->localized_title }}</span>
                            @endif
                            @if ($item->localized_description)
                                <span class="mt-1 line-clamp-2 block text-sm leading-5 text-white/75">{{ $item->localized_description }}</span>
                            @endif
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </x-container>

    <dialog class="gallery-lightbox m-auto max-h-[92vh] w-[min(94vw,80rem)] overflow-visible bg-transparent p-0 text-white backdrop:bg-black/85" data-gallery-dialog aria-label="{{ __('site.gallery_image_viewer') }}">
        <div class="relative overflow-hidden bg-[#111] shadow-[0_28px_80px_rgba(0,0,0,0.5)]">
            <button type="button" class="absolute end-3 top-3 z-10 grid size-11 place-items-center border border-white/30 bg-black/65 text-white transition hover:bg-black" data-gallery-close aria-label="{{ __('site.close') }}">
                <svg aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
            <img src="" alt="" class="max-h-[78vh] w-full object-contain" data-gallery-dialog-image>
            <div class="hidden border-t border-white/10 bg-[#17191c] px-5 py-4 sm:px-7" data-gallery-dialog-caption>
                <h3 class="font-display text-xl font-extrabold" data-gallery-dialog-title></h3>
                <p class="mt-1 text-sm leading-6 text-white/70" data-gallery-dialog-description></p>
            </div>
        </div>
    </dialog>
</section>
