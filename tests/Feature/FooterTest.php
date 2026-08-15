<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Support\BusinessSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_uses_business_settings_routes_actions_and_dynamic_year(): void
    {
        $this->settings([
            'business_name' => 'Workshop Name',
            'logo' => 'branding/logo.png',
            'footer_description_en' => 'Care for tyres, wheels and vehicles.',
            'phone_number' => '+961 81 805 838',
            'whatsapp_number' => '+961 81 805 838',
            'email' => 'hello@example.com',
            'address' => 'Tyre',
            'google_maps_url' => 'https://maps.google.com/?q=Tyre',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Website footer"', false)
            ->assertSee('/storage/branding/logo.png', false)
            ->assertSee('Care for tyres, wheels and vehicles.')
            ->assertSee('href="tel:+96181805838"', false)
            ->assertSee('https://wa.me/96181805838', false)
            ->assertSee('mailto:hello@example.com', false)
            ->assertSee('https://maps.google.com/?q=Tyre', false)
            ->assertSee(route('products.index'), false)
            ->assertSee((string) now()->year)
            ->assertSee('All rights reserved.');
    }

    public function test_empty_social_links_do_not_render_and_configured_links_do(): void
    {
        $this->settings(['facebook_url' => null, 'instagram_url' => null, 'tiktok_url' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('aria-label="Facebook"', false)
            ->assertDontSee('aria-label="Instagram"', false)
            ->assertDontSee('aria-label="TikTok"', false);

        $this->settings(['instagram_url' => 'https://instagram.com/example']);

        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Instagram"', false)
            ->assertSee('https://instagram.com/example', false);
    }

    public function test_arabic_footer_uses_localized_content_and_rtl_document(): void
    {
        $this->settings([
            'business_name' => 'Workshop Name',
            'footer_description_en' => 'English footer',
            'footer_description_ar' => 'وصف عربي للتذييل',
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('وصف عربي للتذييل')
            ->assertSee('جميع الحقوق محفوظة.');
    }

    private function settings(array $attributes): void
    {
        BusinessSetting::query()->firstOrCreate([], config('business.defaults'))->update($attributes);
        app(BusinessSettings::class)->forget();
    }
}
