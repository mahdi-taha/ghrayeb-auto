{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ url('/') }}</loc></url>
    <url><loc>{{ route('products.index') }}</loc></url>
@foreach ($products as $product)
    <url>
        <loc>{{ route('products.show', $product) }}</loc>
        @if ($product->updated_at)<lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>@endif
    </url>
@endforeach
</urlset>
