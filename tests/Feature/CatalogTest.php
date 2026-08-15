<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_test_suite_uses_isolated_in_memory_database(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_homepage_orders_featured_categories_first_without_hiding_other_active_categories(): void
    {
        $regular = $this->category(['name_en' => 'Regular Category', 'slug' => 'regular', 'sort_order' => 1]);
        $featured = $this->category(['name_en' => 'Featured Category', 'slug' => 'featured', 'is_featured' => true, 'sort_order' => 20]);
        $inactive = $this->category(['name_en' => 'Inactive Category', 'slug' => 'inactive', 'is_active' => false, 'is_featured' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([$featured->name_en, $regular->name_en])
            ->assertDontSee($inactive->name_en);
    }

    public function test_catalog_hides_inactive_products_and_products_in_inactive_categories(): void
    {
        $activeCategory = $this->category();
        $inactiveCategory = $this->category(['name_en' => 'Inactive Parent', 'slug' => 'inactive-parent', 'is_active' => false]);
        $visible = $this->product($activeCategory, ['name_en' => 'Visible Product', 'slug' => 'visible-product']);
        $inactive = $this->product($activeCategory, ['name_en' => 'Inactive Product', 'slug' => 'inactive-product', 'is_active' => false]);
        $hiddenByCategory = $this->product($inactiveCategory, ['name_en' => 'Hidden Parent Product', 'slug' => 'hidden-parent-product']);

        $this->get('/products')
            ->assertOk()
            ->assertSee($visible->name_en)
            ->assertDontSee($inactive->name_en)
            ->assertDontSee($hiddenByCategory->name_en);
    }

    public function test_category_filtering_returns_only_matching_active_products(): void
    {
        $tyres = $this->category(['name_en' => 'Tyres', 'slug' => 'tyres']);
        $wheels = $this->category(['name_en' => 'Wheels', 'slug' => 'wheels']);
        $tyreProduct = $this->product($tyres, ['name_en' => 'Tyre Product', 'slug' => 'tyre-product']);
        $wheelProduct = $this->product($wheels, ['name_en' => 'Wheel Product', 'slug' => 'wheel-product']);

        $this->get('/products?category=tyres')
            ->assertOk()
            ->assertSee($tyreProduct->name_en)
            ->assertDontSee($wheelProduct->name_en);
    }

    public function test_featured_products_are_first_without_hiding_regular_products(): void
    {
        $category = $this->category();
        $regular = $this->product($category, [
            'name_en' => 'Regular Product',
            'slug' => 'regular-product',
            'sort_order' => 1,
        ]);
        $featured = $this->product($category, [
            'name_en' => 'Featured Product',
            'slug' => 'featured-product',
            'is_featured' => true,
            'sort_order' => 20,
        ]);

        $this->get('/products')
            ->assertOk()
            ->assertSeeInOrder([$featured->name_en, $regular->name_en]);
    }

    public function test_product_detail_resolves_by_slug_and_renders_arabic_content_and_specifications(): void
    {
        $category = $this->category(['name_ar' => 'الإطارات']);
        $product = $this->product($category, [
            'name_en' => 'Road Tyre',
            'name_ar' => 'إطار للطرق',
            'slug' => 'road-tyre',
            'description_ar' => '<p>وصف المنتج باللغة العربية.</p>',
            'specifications' => [[
                'label_en' => 'Size',
                'label_ar' => 'المقاس',
                'value_en' => 'Example size',
                'value_ar' => 'مقاس تجريبي',
            ]],
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get('/products/'.$product->slug)
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('إطار للطرق')
            ->assertSee('وصف المنتج باللغة العربية.', false)
            ->assertSee('المقاس')
            ->assertSee('مقاس تجريبي');
    }

    public function test_price_is_hidden_or_shown_according_to_show_price(): void
    {
        $category = $this->category();
        $hiddenPrice = $this->product($category, [
            'name_en' => 'Hidden Price Product',
            'slug' => 'hidden-price-product',
            'price' => 1234.50,
            'show_price' => false,
        ]);
        $shownPrice = $this->product($category, [
            'name_en' => 'Shown Price Product',
            'slug' => 'shown-price-product',
            'price' => 987.65,
            'show_price' => true,
        ]);

        $this->get('/products/'.$hiddenPrice->slug)
            ->assertOk()
            ->assertDontSee('1,234.50');

        $this->get('/products/'.$shownPrice->slug)
            ->assertOk()
            ->assertSee('987.65');
    }

    public function test_catalog_cards_format_symbol_prices_tightly_and_support_mixed_direction_names(): void
    {
        $settings = \App\Models\BusinessSetting::query()->firstOrCreate([], config('business.defaults'));
        $settings->update(['currency_code' => '$']);
        app(\App\Support\BusinessSettings::class)->forget();

        $product = $this->product($this->category(), [
            'name_en' => 'إطار Performance 205/55 R16',
            'slug' => 'mixed-direction-product',
            'price' => 50,
            'show_price' => true,
        ]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('<bdi dir="auto" class="line-clamp-2">'.$product->name_en.'</bdi>', false)
            ->assertSee('$50.00')
            ->assertDontSee('$ 50.00');
    }

    public function test_catalog_product_images_use_responsive_containment_without_cropping(): void
    {
        $product = $this->product($this->category(), [
            'main_image' => 'products/full-tyre.jpg',
            'slug' => 'full-tyre-image',
        ]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('aspect-[3/2]', false)
            ->assertSee('object-contain object-center', false)
            ->assertDontSee('h-48 shrink-0', false);
    }

    public function test_catalog_pagination_keeps_the_category_filter_query_string(): void
    {
        $category = $this->category(['name_en' => 'Tyres', 'slug' => 'tyres']);

        foreach (range(1, 13) as $index) {
            $this->product($category, [
                'name_en' => "Tyre Product {$index}",
                'slug' => "tyre-product-{$index}",
            ]);
        }

        $this->get('/products?category=tyres')
            ->assertOk()
            ->assertSee('category=tyres&amp;page=2', false);
    }

    public function test_product_gallery_is_interactive_only_when_multiple_images_exist(): void
    {
        $category = $this->category();
        $multiple = $this->product($category, [
            'name_en' => 'Multiple Image Product',
            'slug' => 'multiple-image-product',
            'main_image' => 'products/main.jpg',
            'gallery' => ['products/second.jpg', 'products/third.jpg'],
        ]);

        $this->get('/products/'.$multiple->slug)
            ->assertOk()
            ->assertSee('data-product-main-image', false)
            ->assertSee('data-product-thumbnail', false)
            ->assertSee('aria-pressed="true"', false)
            ->assertSee('object-contain', false);

        $single = $this->product($category, [
            'name_en' => 'Single Image Product',
            'slug' => 'single-image-product',
            'main_image' => 'products/single.jpg',
        ]);

        $this->get('/products/'.$single->slug)
            ->assertOk()
            ->assertSee('data-product-main-image', false)
            ->assertDontSee('data-product-thumbnail', false);
    }

    public function test_product_inquiry_uses_localized_whatsapp_message_and_tight_price_format(): void
    {
        $settings = \App\Models\BusinessSetting::query()->firstOrCreate([], config('business.defaults'));
        $settings->update(['whatsapp_number' => '+961 70 123 456', 'currency_code' => '$']);
        app(\App\Support\BusinessSettings::class)->forget();

        $product = $this->product($this->category(), [
            'name_en' => 'Premium Tyre 235/40 R18',
            'slug' => 'premium-tyre',
            'price' => 50,
            'show_price' => true,
        ]);
        $message = __('site.whatsapp_product_message', ['product' => $product->name_en]);

        $this->get('/products/'.$product->slug)
            ->assertOk()
            ->assertSee('$50.00')
            ->assertDontSee('$ 50.00')
            ->assertSee('https://wa.me/96170123456?text='.rawurlencode($message), false)
            ->assertSee('Ask About This Product');
    }

    public function test_related_products_are_active_same_category_limited_and_exclude_current_product(): void
    {
        $category = $this->category();
        $otherCategory = $this->category(['name_en' => 'Other Category', 'slug' => 'other-category']);
        $current = $this->product($category, ['name_en' => 'Current Product', 'slug' => 'current-product']);

        foreach (range(1, 5) as $index) {
            $this->product($category, ['name_en' => "Related {$index}", 'slug' => "related-{$index}", 'sort_order' => $index]);
        }

        $this->product($category, ['name_en' => 'Inactive Related', 'slug' => 'inactive-related', 'is_active' => false]);
        $this->product($otherCategory, ['name_en' => 'Wrong Category', 'slug' => 'wrong-category']);

        $this->get('/products/'.$current->slug)
            ->assertOk()
            ->assertSee('Related Products')
            ->assertSeeInOrder(['Related 1', 'Related 2', 'Related 3', 'Related 4'])
            ->assertDontSee('Related 5')
            ->assertDontSee('Inactive Related')
            ->assertDontSee('Wrong Category');
    }

    public function test_empty_specifications_and_related_products_hide_their_sections(): void
    {
        $product = $this->product($this->category(), ['specifications' => []]);

        $this->get('/products/'.$product->slug)
            ->assertOk()
            ->assertDontSee('id="specifications-heading"', false)
            ->assertDontSee('id="related-products-heading"', false);
    }

    public function test_inactive_product_detail_is_not_publicly_accessible(): void
    {
        $product = $this->product($this->category(), ['is_active' => false]);

        $this->get('/products/'.$product->slug)->assertNotFound();
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = $this->category();
        $this->product($category);

        $this->expectException(QueryException::class);

        $category->delete();
    }

    private function category(array $overrides = []): ProductCategory
    {
        return ProductCategory::query()->create(array_replace([
            'name_en' => 'Accessories',
            'name_ar' => null,
            'slug' => 'accessories',
            'description_en' => 'Automotive accessories.',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ], $overrides));
    }

    private function product(ProductCategory $category, array $overrides = []): Product
    {
        return Product::query()->create(array_replace([
            'product_category_id' => $category->id,
            'name_en' => 'Catalog Product',
            'name_ar' => null,
            'slug' => 'catalog-product',
            'short_description_en' => 'A catalog product description.',
            'price' => null,
            'show_price' => false,
            'is_available' => true,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ], $overrides));
    }
}
