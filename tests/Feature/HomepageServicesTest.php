<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_section_is_hidden_when_there_are_no_active_services(): void
    {
        Service::query()->create($this->serviceAttributes([
            'name' => 'Inactive Service',
            'is_active' => false,
        ]));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Professional Care For Your Vehicle');
    }

    public function test_featured_services_are_first_without_hiding_other_active_services(): void
    {
        Service::query()->create($this->serviceAttributes([
            'name' => 'Service B',
            'sort_order' => 10,
        ]));
        Service::query()->create($this->serviceAttributes([
            'name' => 'Service A',
            'is_featured' => true,
            'sort_order' => 30,
        ]));
        Service::query()->create($this->serviceAttributes([
            'name' => 'Service C',
            'sort_order' => 20,
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Service A', 'Service B', 'Service C']);
    }

    public function test_inactive_featured_service_is_not_displayed(): void
    {
        Service::query()->create($this->serviceAttributes([
            'name' => 'Inactive Featured',
            'is_featured' => true,
            'is_active' => false,
        ]));
        Service::query()->create($this->serviceAttributes([
            'name' => 'Active Regular',
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSee('Active Regular')
            ->assertDontSee('Inactive Featured');
    }

    public function test_arabic_locale_uses_rtl_and_localized_service_content(): void
    {
        Service::query()->create($this->serviceAttributes([
            'name_en' => 'Wheel Balancing',
            'name_ar' => 'ترصيص الإطارات',
            'short_description_en' => 'Precise wheel balancing.',
            'short_description_ar' => 'ترصيص دقيق للعجلات.',
        ]));

        $this->withSession(['locale' => 'ar'])
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('ترصيص الإطارات')
            ->assertSee('ترصيص دقيق للعجلات.');
    }

    public function test_english_is_the_default_locale_and_language_switch_persists(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);

        $this->from('/')->get('/language/ar')->assertRedirect('/');

        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false);
    }

    public function test_active_services_are_used_when_none_are_featured_and_are_limited_to_six(): void
    {
        foreach (range(1, 7) as $index) {
            Service::query()->create($this->serviceAttributes([
                'name' => "Service {$index}",
                'sort_order' => $index,
            ]));
        }

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Service 1', 'Service 2', 'Service 3', 'Service 4', 'Service 5', 'Service 6'])
            ->assertDontSee('Service 7');
    }

    private function serviceAttributes(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Tyre Service',
            'short_description' => 'Professional automotive care.',
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides);
    }
}
