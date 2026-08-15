<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GallerySectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_section_is_hidden_without_active_items(): void
    {
        $this->galleryItem(['title_en' => 'Inactive Work', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Inactive Work')
            ->assertDontSee('gallery-mosaic', false);
    }

    public function test_active_items_are_public_and_inactive_items_are_hidden(): void
    {
        $this->galleryItem(['title_en' => 'Visible Work', 'image' => 'gallery/visible.jpg']);
        $this->galleryItem(['title_en' => 'Hidden Work', 'image' => 'gallery/hidden.jpg', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Visible Work')
            ->assertSee('/storage/gallery/visible.jpg')
            ->assertDontSee('Hidden Work');
    }

    public function test_featured_items_are_first_without_hiding_normal_active_items(): void
    {
        $regular = $this->galleryItem(['title_en' => 'Regular Work', 'image' => 'gallery/regular.jpg', 'sort_order' => 1]);
        $featured = $this->galleryItem(['title_en' => 'Featured Work', 'image' => 'gallery/featured.jpg', 'is_featured' => true, 'sort_order' => 20]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([$featured->title_en, $regular->title_en]);
    }

    public function test_arabic_caption_renders_in_rtl(): void
    {
        $this->galleryItem([
            'title_en' => 'English Work',
            'title_ar' => 'عمل من الورشة',
            'description_ar' => 'وصف عربي للصورة.',
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('عمل من الورشة')
            ->assertSee('وصف عربي للصورة.');
    }

    public function test_caption_markup_is_omitted_when_caption_is_empty_and_lightbox_remains_available(): void
    {
        $this->galleryItem(['title_en' => null, 'description_en' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('gallery-caption', false)
            ->assertSee('data-gallery-dialog', false)
            ->assertSee('data-gallery-trigger', false);
    }

    public function test_gallery_exposes_the_item_count_layout_class_and_limits_homepage_to_eight_items(): void
    {
        foreach (range(1, 9) as $position) {
            $this->galleryItem([
                'image' => "gallery/work-{$position}.jpg",
                'title_en' => "Workshop Work {$position}",
                'sort_order' => $position,
            ]);
        }

        $response = $this->get('/')->assertOk();

        $response
            ->assertSee('gallery-mosaic--count-8', false)
            ->assertSee('Workshop Work 1')
            ->assertSee('Workshop Work 8')
            ->assertDontSee('Workshop Work 9');

        $this->assertSame(8, substr_count($response->getContent(), 'data-gallery-trigger'));
    }

    private function galleryItem(array $overrides = []): GalleryItem
    {
        return GalleryItem::query()->create(array_replace([
            'image' => 'gallery/work.jpg',
            'title_en' => 'Workshop Work',
            'title_ar' => null,
            'description_en' => null,
            'description_ar' => null,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }
}
