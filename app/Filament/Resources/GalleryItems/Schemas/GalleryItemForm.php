<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Image')->schema([
                    OptimizedImageUpload::make('image', 'gallery', 'photography')
                        ->required(),
                ]),
                Section::make('English caption')->schema([
                    TextInput::make('title_en')->label('Title')->maxLength(255),
                    Textarea::make('description_en')->label('Description')->rows(3)->columnSpanFull(),
                ]),
                Section::make('Arabic caption')->schema([
                    TextInput::make('title_ar')->label('Title (Arabic)')->maxLength(255),
                    Textarea::make('description_ar')->label('Description (Arabic)')->rows(3)->columnSpanFull(),
                ]),
                Section::make('Homepage display')->schema([
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
