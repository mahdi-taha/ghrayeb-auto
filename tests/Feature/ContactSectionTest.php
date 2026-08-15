<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Support\BusinessSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_details_actions_and_multiline_hours_render_from_business_settings(): void
    {
        $this->settings([
            'show_contact_section' => true,
            'phone_number' => '+961 1 234 567',
            'whatsapp_number' => '+961 70 123 456',
            'email' => 'hello@example.com',
            'address' => 'Workshop Road, Beirut',
            'google_maps_url' => 'https://maps.google.com/?q=Beirut',
            'opening_hours' => "Monday - Friday: 8:00 - 18:00\nSaturday: 8:00 - 15:00",
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="contact"', false)
            ->assertSee('href="tel:+9611234567"', false)
            ->assertSee('href="https://wa.me/96170123456"', false)
            ->assertSee('href="mailto:hello@example.com"', false)
            ->assertSee('Monday - Friday: 8:00 - 18:00')
            ->assertSee('Saturday: 8:00 - 15:00')
            ->assertSee('https://maps.google.com/?q=Beirut', false)
            ->assertDontSee('<iframe', false);
    }

    public function test_safe_google_embed_url_renders_an_iframe(): void
    {
        $this->settings([
            'show_contact_section' => true,
            'google_maps_url' => 'https://maps.google.com/?q=Beirut',
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=test',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('<iframe', false)
            ->assertSee('https://www.google.com/maps/embed?pb=test', false);
    }

    public function test_normal_directions_url_uses_location_panel_and_never_an_iframe(): void
    {
        $this->settings([
            'show_contact_section' => true,
            'address' => 'Tyre',
            'google_maps_url' => 'https://maps.google.com/?q=Tyre',
            'google_maps_embed_url' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('contact-map__fallback', false)
            ->assertSee('https://maps.google.com/?q=Tyre', false)
            ->assertDontSee('<iframe', false);
    }

    public function test_missing_fields_and_disabled_section_are_hidden_cleanly(): void
    {
        $this->settings([
            'show_contact_section' => true,
            'phone_number' => null,
            'whatsapp_number' => null,
            'email' => null,
            'address' => null,
            'google_maps_url' => null,
            'opening_hours' => null,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('href="tel:', false)
            ->assertDontSee('https://wa.me/', false);

        $this->settings(['show_contact_section' => false]);

        $this->get('/')->assertOk()->assertDontSee('class="contact-section', false);
    }

    public function test_arabic_cms_content_renders_in_rtl(): void
    {
        $this->settings([
            'show_contact_section' => true,
            'contact_heading_en' => 'Visit our workshop',
            'contact_heading_ar' => 'زوروا ورشتنا',
            'contact_description_ar' => 'تواصلوا معنا للحصول على المساعدة.',
        ]);

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('زوروا ورشتنا')
            ->assertSee('تواصلوا معنا للحصول على المساعدة.');
    }

    private function settings(array $attributes): void
    {
        BusinessSetting::query()->firstOrCreate([], config('business.defaults'))->update($attributes);
        app(BusinessSettings::class)->forget();
    }
}
