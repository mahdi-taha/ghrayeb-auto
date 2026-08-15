<?php

namespace App\Filament\Resources\ProductCategories\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProductCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public')->square(),
                TextColumn::make('name_en')->label('English name')->searchable()->sortable(),
                TextColumn::make('name_ar')->label('Arabic name')->searchable()->toggleable(),
                ToggleColumn::make('is_active')->label('Active'),
                ToggleColumn::make('is_featured')->label('Featured'),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}
