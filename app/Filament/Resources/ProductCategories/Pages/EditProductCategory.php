<?php

namespace App\Filament\Resources\ProductCategories\Pages;

use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Models\ProductCategory;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductCategory extends EditRecord
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (ProductCategory $record): bool => $record->products()->exists())
                ->tooltip(fn (ProductCategory $record): ?string => $record->products()->exists()
                    ? 'Move or delete products in this category before deleting it.'
                    : null),
        ];
    }
}
