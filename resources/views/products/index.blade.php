<x-layouts.app :seo="app(\App\Support\SeoMetadata::class)->catalog()">
    <section class="bg-mist pb-14 pt-8 sm:pb-16 sm:pt-10 lg:pb-20 lg:pt-12">
        <x-container>
            <div class="max-w-3xl">
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-steel">{{ __('site.product_range') }}</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold tracking-[-0.04em] text-ink sm:text-5xl">{{ __('site.catalog_heading') }}</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-steel">{{ __('site.catalog_description') }}</p>
            </div>

            <form method="GET" action="{{ route('products.index') }}" class="mt-7 flex max-w-lg flex-col gap-3 sm:flex-row sm:items-stretch" aria-label="{{ __('site.category') }}">
                <label for="category" class="sr-only">{{ __('site.category') }}</label>
                <select id="category" name="category" class="min-h-12 min-w-0 rounded-sm border border-line bg-white px-4 text-sm font-semibold text-ink sm:w-80">
                    <option value="">{{ __('site.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>{{ $category->localized_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="button-primary">{{ __('site.filter') }}</button>
            </form>

            @if ($products->isNotEmpty())
                <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-10">{{ $products->links() }}</div>
            @else
                <div class="mt-10 border border-line bg-white px-6 py-10 text-center text-steel">{{ __('site.no_products') }}</div>
            @endif
        </x-container>
    </section>
</x-layouts.app>
