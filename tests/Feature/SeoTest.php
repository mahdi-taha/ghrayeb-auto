<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://demo.example',
            'filesystems.disks.public.url' => 'https://demo.example/storage',
        ]);
        URL::forceRootUrl('https://demo.example');
        URL::forceScheme('https');
    }

    public function test_homepage_uses_localized_cms_metadata_and_absolute_social_image(): void
    {
        Storage::fake('public');
        config(['filesystems.disks.public.url' => 'https://demo.example/storage']);
        Storage::disk('public')->put('hero/demo.webp', 'image');

        BusinessSetting::query()->first()->update([
            'business_name' => 'Workshop',
            'seo_title_en' => 'Workshop SEO Title',
            'seo_description_en' => 'A safe workshop description.',
            'hero_image' => 'hero/demo.webp',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Workshop SEO Title</title>', false)
            ->assertSee('<meta name="description" content="A safe workshop description.">', false)
            ->assertSee('<link rel="canonical" href="https://demo.example">', false)
            ->assertSee('<meta property="og:url" content="https://demo.example">', false)
            ->assertSee('https://demo.example/storage/hero/demo.webp', false)
            ->assertDontSee('localhost', false)
            ->assertDontSee('C:\\xampp', false);
    }

    public function test_product_metadata_is_localized_plain_trimmed_and_canonical(): void
    {
        [$product] = $this->createProducts();

        $this->withSession(['locale' => 'ar'])
            ->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('<title>إطار تجريبي | Automotive Service</title>', false)
            ->assertSee('<meta name="description" content="وصف عربي قصير">', false)
            ->assertSee('<link rel="canonical" href="https://demo.example/products/active-product">', false)
            ->assertSee('<meta property="og:locale" content="ar_AR">', false);
    }

    public function test_filtered_catalog_has_clean_canonical_without_query_string(): void
    {
        $category = ProductCategory::query()->create([
            'name_en' => 'Tyres',
            'slug' => 'tyres',
            'is_active' => true,
        ]);

        $this->get('/products?category='.$category->slug)
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://demo.example/products">', false)
            ->assertDontSee('canonical" href="https://demo.example/products?category=', false);
    }

    public function test_sitemap_contains_active_products_and_excludes_inactive_products(): void
    {
        [$active, $inactive] = $this->createProducts();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('https://demo.example', false)
            ->assertSee('https://demo.example/products', false)
            ->assertSee(route('products.show', $active), false)
            ->assertDontSee(route('products.show', $inactive), false);
    }

    public function test_robots_uses_the_environment_generated_sitemap_url(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Sitemap: https://demo.example/sitemap.xml', false);
    }

    public function test_branded_404_and_arabic_error_copy_render(): void
    {
        $this->get('/missing-page')
            ->assertNotFound()
            ->assertSee('Page Not Found')
            ->assertSee('Back Home')
            ->assertDontSee('Stack trace');

        $this->withSession(['locale' => 'ar'])
            ->get('/missing-page-ar')
            ->assertNotFound()
            ->assertSee('dir="rtl"', false)
            ->assertSee('الصفحة غير موجودة')
            ->assertSee('العودة إلى الرئيسية');
    }

    public function test_500_view_contains_no_exception_details_and_needs_no_business_data(): void
    {
        $html = view('errors.500')->render();

        $this->assertStringContainsString('Something Went Wrong', $html);
        $this->assertStringContainsString('Back Home', $html);
        $this->assertStringNotContainsString('exception', strtolower($html));
        $this->assertStringNotContainsString('stack trace', strtolower($html));
    }

    private function createProducts(): array
    {
        BusinessSetting::query()->first()->update(['business_name' => 'Automotive Service']);

        $category = ProductCategory::query()->create([
            'name_en' => 'Tyres',
            'name_ar' => 'إطارات',
            'slug' => 'tyres',
            'is_active' => true,
        ]);

        $active = Product::query()->create([
            'product_category_id' => $category->id,
            'name_en' => 'Demo Tyre',
            'name_ar' => 'إطار تجريبي',
            'slug' => 'active-product',
            'short_description_en' => 'Short English description',
            'short_description_ar' => 'وصف عربي قصير',
            'is_active' => true,
        ]);

        $inactive = Product::query()->create([
            'product_category_id' => $category->id,
            'name_en' => 'Hidden Product',
            'slug' => 'inactive-product',
            'is_active' => false,
        ]);

        return [$active, $inactive];
    }
}
