<?php

namespace Tests\Feature;

use App\Filament\Pages\BusinessSettings;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Support\Icons\Heroicon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_home_redirects_an_admin_to_business_settings_without_a_dashboard_route(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect(BusinessSettings::getUrl(panel: 'admin'));

        $this->assertFalse(Route::has('filament.admin.pages.dashboard'));
        $this->actingAs($admin)->get(BusinessSettings::getUrl(panel: 'admin'))->assertOk();
    }

    public function test_admin_login_and_all_navigation_destinations_remain_available(): void
    {
        $this->get('/admin/login')->assertOk();

        $admin = User::factory()->create(['is_admin' => true]);

        foreach ([
            BusinessSettings::getUrl(panel: 'admin'),
            ServiceResource::getUrl(panel: 'admin'),
            ProductCategoryResource::getUrl(panel: 'admin'),
            ProductResource::getUrl(panel: 'admin'),
            GalleryItemResource::getUrl(panel: 'admin'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_admin_navigation_uses_distinct_icons_and_logical_sort_order(): void
    {
        $items = [
            [BusinessSettings::class, Heroicon::OutlinedCog6Tooth, 1],
            [ServiceResource::class, Heroicon::OutlinedWrenchScrewdriver, 2],
            [ProductCategoryResource::class, Heroicon::OutlinedSquares2x2, 3],
            [ProductResource::class, Heroicon::OutlinedCube, 4],
            [GalleryItemResource::class, Heroicon::OutlinedPhoto, 5],
        ];

        foreach ($items as [$item, $icon, $sort]) {
            $this->assertSame($icon, $item::getNavigationIcon());
            $this->assertSame($sort, $item::getNavigationSort());
        }

        $this->assertCount(5, array_unique(array_map(fn ($item) => $item[1]->value, $items)));
    }
}
