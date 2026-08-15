<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('English content')
                    ->schema([
                        TextInput::make('name_en')
                            ->label('Service name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('short_description_en')
                            ->label('Short description')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('description_en')
                            ->label('Full description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Arabic content')
                    ->schema([
                        TextInput::make('name_ar')
                            ->label('Service name (Arabic)')
                            ->maxLength(255),
                        Textarea::make('short_description_ar')
                            ->label('Short description (Arabic)')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('description_ar')
                            ->label('Full description (Arabic)')
                            ->columnSpanFull(),
                    ]),
                Section::make('Presentation')
                    ->schema([
                        OptimizedImageUpload::make('image', 'services'),
                        TextInput::make('icon')
                            ->maxLength(100)
                            ->helperText('Optional icon name reserved for future presentation use.'),
                    ])
                    ->columns(2),
                Section::make('Homepage visibility')
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Featured on homepage')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Display order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }
}
