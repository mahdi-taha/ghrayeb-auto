<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\Service;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\HomepageSections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_navigation_matches_rendered_gallery_for_desktop_mobile_and_footer(): void
    {
        $withoutGallery = $this->get('/')->assertOk();

        $withoutGallery
            ->assertDontSee('id="our-work"', false)
            ->assertSee(route('products.index'), false);
        $this->assertSame(0, substr_count($withoutGallery->getContent(), '>Our Work</a>'));

        GalleryItem::query()->create([
            'image' => 'gallery/work.jpg',
            'title_en' => 'Visible Gallery Work',
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        app()->forgetInstance(HomepageSections::class);

        $withGallery = $this->get('/')->assertOk();

        $withGallery->assertSee('id="our-work"', false);
        $this->assertSame(3, substr_count($withGallery->getContent(), '>Our Work</a>'));
    }

    public function test_services_anchor_is_hidden_without_active_services_and_shown_with_content(): void
    {
        $withoutServices = $this->get('/')->assertOk();
        $this->assertSame(0, substr_count($withoutServices->getContent(), '>Services</a>'));

        Service::query()->create([
            'name_en' => 'Active Service',
            'short_description_en' => 'Active service description.',
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        app()->forgetInstance(HomepageSections::class);

        $response = $this->get('/')->assertOk();

        $response->assertSee('id="services"', false);
        $this->assertSame(3, substr_count($response->getContent(), '>Services</a>'));
    }

    public function test_homepage_and_product_routes_render_the_correct_initial_active_state(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-homepage-nav', false)
            ->assertSee('data-initial-nav="home"', false)
            ->assertSee('data-nav-section="home"', false);

        $category = ProductCategory::query()->create([
            'name_en' => 'Tyres',
            'slug' => 'tyres-active-navigation',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);
        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'name_en' => 'Navigation Tyre',
            'slug' => 'navigation-tyre',
            'show_price' => false,
            'is_available' => true,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        foreach (['/products', '/products/'.$product->slug] as $url) {
            $response = $this->get($url)->assertOk();
            $response
                ->assertSee('data-initial-nav="products"', false)
                ->assertSee('data-nav-key="products"', false)
                ->assertSee('aria-current="page"', false);
        }
    }
}
