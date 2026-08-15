<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('English content')->schema([
                    TextInput::make('name_en')
                        ->label('Product name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rules(['alpha_dash:ascii']),
                    Textarea::make('short_description_en')
                        ->label('Short description')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),
                    RichEditor::make('description_en')
                        ->label('Full description')
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make('Arabic content')->schema([
                    TextInput::make('name_ar')->label('Product name (Arabic)')->maxLength(255),
                    Textarea::make('short_description_ar')
                        ->label('Short description (Arabic)')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),
                    RichEditor::make('description_ar')
                        ->label('Full description (Arabic)')
                        ->columnSpanFull(),
                ]),
                Section::make('Category and pricing')->schema([
                    Select::make('product_category_id')
                        ->label('Category')
                        ->relationship('category', 'name_en')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('price')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),
                    Toggle::make('show_price')->label('Show price publicly')->default(false),
                    Toggle::make('is_available')->label('Available')->default(true),
                ])->columns(2),
                Section::make('Images')->schema([
                    OptimizedImageUpload::make('main_image', 'products/main'),
                    OptimizedImageUpload::make('gallery', 'products/gallery', withEditor: false)
                        ->label('Gallery images')
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(8),
                ])->columns(2),
                Section::make('Specifications')->schema([
                    Repeater::make('specifications')
                        ->schema([
                            TextInput::make('label_en')->label('English label')->required()->maxLength(100),
                            TextInput::make('value_en')->label('English value')->required()->maxLength(255),
                            TextInput::make('label_ar')->label('Arabic label')->maxLength(100),
                            TextInput::make('value_ar')->label('Arabic value')->maxLength(255),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Add specification')
                        ->reorderable()
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
                Section::make('Catalog visibility')->schema([
                    Toggle::make('is_featured')->label('Featured')->default(false),
                    Toggle::make('is_active')->label('Active')->default(true),
                    TextInput::make('sort_order')
                        ->label('Display order')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ])->columns(3),
            ]);
    }
}
