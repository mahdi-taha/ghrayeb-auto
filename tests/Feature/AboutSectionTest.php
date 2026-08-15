<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Support\BusinessSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_section_is_hidden_when_disabled(): void
    {
        $this->updateSettings([
            'show_about_section' => false,
            'about_heading_en' => 'Hidden About Heading',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Hidden About Heading')
            ->assertDontSee('class="about-section"', false);
    }

    public function test_enabled_section_without_content_does_not_render_empty_wrappers(): void
    {
        $this->updateSettings(['show_about_section' => true]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('class="about-section"', false);
    }

    public function test_english_content_and_any_number_of_trust_points_render_without_cards(): void
    {
        $points = collect(range(1, 4))->map(fn (int $index): array => [
            'title_en' => "Trust Point {$index}",
            'title_ar' => "نقطة {$index}",
            'description_en' => "Description {$index}",
            'description_ar' => "وصف {$index}",
        ])->all();

        $this->updateSettings([
            'show_about_section' => true,
            'about_eyebrow_en' => 'Why Choose Us',
            'about_heading_en' => 'Owner supplied heading',
            'about_description_en' => 'Owner supplied business description.',
            'about_points' => $points,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Why Choose Us')
            ->assertSee('Owner supplied heading')
            ->assertSeeInOrder(['Trust Point 1', 'Trust Point 2', 'Trust Point 3', 'Trust Point 4'])
            ->assertSee('class="why-points', false)
            ->assertSee('class="about-visual__fallback"', false);
    }

    public function test_arabic_content_and_points_render_in_rtl(): void
    {
        $this->updateSettings([
            'show_about_section' => true,
            'about_eyebrow_en' => 'About Us',
            'about_eyebrow_ar' => 'لماذا نحن',
            'about_heading_en' => 'English heading',
            'about_heading_ar' => 'عنوان عربي',
            'about_description_ar' => 'محتوى عربي يضيفه صاحب العمل.',
            'about_points' => [[
                'title_en' => 'English point',
                'title_ar' => 'نقطة عربية',
                'description_en' => null,
                'description_ar' => 'وصف عربي',
            ]],
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('لماذا نحن')
            ->assertSee('عنوان عربي')
            ->assertSee('نقطة عربية')
            ->assertSee('وصف عربي');
    }

    public function test_about_image_uses_the_public_storage_url_and_points_area_is_omitted_when_empty(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('about/workshop.jpg', 'image');

        $this->updateSettings([
            'show_about_section' => true,
            'about_heading_en' => 'About Image Heading',
            'about_image' => 'about/workshop.jpg',
            'about_points' => [],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('about/workshop.jpg'))
            ->assertDontSee('class="why-points', false);
    }

    public function test_trust_points_render_independently_when_about_is_disabled(): void
    {
        $this->updateSettings([
            'show_about_section' => false,
            'about_points' => [[
                'title_en' => 'Independent trust point',
                'description_en' => 'Trust point description.',
            ]],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('class="about-section"', false)
            ->assertSee('class="why-section', false)
            ->assertSee('Independent trust point');
    }

    public function test_about_brand_color_uses_the_current_primary_color_for_multiple_color_families(): void
    {
        foreach (['#d40000', '#1457d9', '#16803a', '#e56b12'] as $primaryColor) {
            $this->updateSettings([
                'primary_color' => $primaryColor,
                'show_about_section' => true,
                'about_heading_en' => 'Dynamic brand color',
            ]);

            $this->get('/')
                ->assertOk()
                ->assertSee("--brand-primary: {$primaryColor}", false)
                ->assertSee('class="about-section"', false);
        }
    }

    public function test_about_styles_derive_paint_shades_without_fixed_red_values(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $aboutStyles = substr($css, strpos($css, '.about-section {'), strpos($css, '.why-points {') - strpos($css, '.about-section {'));

        $this->assertStringContainsString('--about-surface: color-mix(in srgb, var(--brand-primary)', $aboutStyles);
        $this->assertStringContainsString('--about-highlight: color-mix(in srgb, var(--brand-primary)', $aboutStyles);
        $this->assertStringNotContainsString('#d40000', strtolower($aboutStyles));
        $this->assertStringNotContainsString('#b80000', strtolower($aboutStyles));
        $this->assertStringNotContainsString('rgba(240,26,26', str_replace(' ', '', strtolower($aboutStyles)));
    }

    private function updateSettings(array $attributes): void
    {
        BusinessSetting::query()->firstOrCreate([], config('business.defaults'))->update($attributes);
        app(BusinessSettings::class)->forget();
    }
}
