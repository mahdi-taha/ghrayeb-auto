<?php

namespace Tests\Feature;

use App\Filament\Pages\BusinessSettings;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Support\Icons\Heroicon;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_dashboard_is_routable_but_hidden_from_navigation(): void
    {
        $this->assertFalse(Dashboard::shouldRegisterNavigation());
        $this->get('/admin')->assertRedirect('/admin/login');
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
