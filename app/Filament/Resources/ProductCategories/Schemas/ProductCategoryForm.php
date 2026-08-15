<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('English content')->schema([
                    TextInput::make('name_en')
                        ->label('Category name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rules(['alpha_dash:ascii']),
                    Textarea::make('description_en')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make('Arabic content')->schema([
                    TextInput::make('name_ar')
                        ->label('Category name (Arabic)')
                        ->maxLength(255),
                    Textarea::make('description_ar')
                        ->label('Description (Arabic)')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
                Section::make('Presentation and visibility')->schema([
                    OptimizedImageUpload::make('image', 'product-categories'),
                    TextInput::make('sort_order')
                        ->label('Display order')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                    Toggle::make('is_featured')->label('Featured on homepage')->default(false),
                    Toggle::make('is_active')->label('Active')->default(true),
                ])->columns(2),
            ]);
    }
}
