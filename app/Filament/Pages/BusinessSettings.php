<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Components\OptimizedImageUpload;
use App\Models\BusinessSetting;
use App\Support\BusinessSettings as BusinessSettingsRepository;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BusinessSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Business settings';

    protected static ?string $title = 'Business settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.business-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = BusinessSetting::query()->firstOrCreate([], config('business.defaults'));
        $this->form->fill($settings->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Branding')->schema([
                    TextInput::make('business_name')->required()->maxLength(255),
                    OptimizedImageUpload::make('logo', 'branding', 'logo'),
                    ColorPicker::make('primary_color')->required(),
                    ColorPicker::make('secondary_color')->required(),
                ])->columns(2),
                Section::make('Contact details')->schema([
                    TextInput::make('phone_number')->tel()->maxLength(40),
                    TextInput::make('whatsapp_number')->tel()->maxLength(40),
                    TextInput::make('email')->email()->maxLength(255),
                    Textarea::make('address')->rows(3),
                    TextInput::make('google_maps_url')->url(),
                    TextInput::make('google_maps_embed_url')
                        ->label('Google Maps embed URL')
                        ->url()
                        ->helperText('Optional. Paste the URL from Google Maps → Share → Embed a map. The normal Maps URL above remains the Directions link.'),
                    TextInput::make('facebook_url')->url(),
                    TextInput::make('instagram_url')->url(),
                    TextInput::make('tiktok_url')->url(),
                    Textarea::make('opening_hours')->rows(4),
                ])->columns(2),
                Section::make('Footer')->schema([
                    Textarea::make('footer_description_en')
                        ->label('Footer description')
                        ->rows(3)
                        ->maxLength(500),
                    Textarea::make('footer_description_ar')
                        ->label('Footer description (Arabic)')
                        ->rows(3)
                        ->maxLength(500),
                ])->columns(2),
                Section::make('Homepage contact section')->schema([
                    Toggle::make('show_contact_section')
                        ->label('Show contact section on homepage')
                        ->default(true)
                        ->columnSpanFull(),
                    TextInput::make('contact_eyebrow_en')->label('Eyebrow')->maxLength(100),
                    TextInput::make('contact_eyebrow_ar')->label('Eyebrow (Arabic)')->maxLength(100),
                    TextInput::make('contact_heading_en')->label('Heading')->maxLength(255),
                    TextInput::make('contact_heading_ar')->label('Heading (Arabic)')->maxLength(255),
                    Textarea::make('contact_description_en')->label('Description')->rows(3),
                    Textarea::make('contact_description_ar')->label('Description (Arabic)')->rows(3),
                ])->columns(2),
                Section::make('Catalog settings')->schema([
                    TextInput::make('currency_code')
                        ->label('Currency code or symbol')
                        ->maxLength(10)
                        ->helperText('Examples: USD, LBP, or €. Leave empty to show prices without a currency label.'),
                ]),
                Section::make('Homepage introduction — English')->schema([
                    TextInput::make('hero_heading_en')->label('Hero heading')->maxLength(255),
                    Textarea::make('hero_description_en')->label('Hero description')->rows(4)->columnSpanFull(),
                    TextInput::make('primary_cta_text_en')->label('Primary CTA text')->maxLength(100),
                    TextInput::make('secondary_cta_text_en')->label('Secondary CTA text')->maxLength(100),
                ])->columns(2),
                Section::make('Homepage introduction — Arabic')->schema([
                    TextInput::make('hero_heading_ar')->label('Hero heading (Arabic)')->maxLength(255),
                    Textarea::make('hero_description_ar')->label('Hero description (Arabic)')->rows(4)->columnSpanFull(),
                    TextInput::make('primary_cta_text_ar')->label('Primary CTA text (Arabic)')->maxLength(100),
                    TextInput::make('secondary_cta_text_ar')->label('Secondary CTA text (Arabic)')->maxLength(100),
                ])->columns(2),
                Section::make('Homepage image')->schema([
                    OptimizedImageUpload::make('hero_image', 'hero', 'photography'),
                ]),
                Section::make('Homepage SEO')->schema([
                    TextInput::make('seo_title_en')->label('SEO title')->maxLength(255),
                    TextInput::make('seo_title_ar')->label('SEO title (Arabic)')->maxLength(255),
                    Textarea::make('seo_description_en')->label('SEO description')->rows(3)->maxLength(500),
                    Textarea::make('seo_description_ar')->label('SEO description (Arabic)')->rows(3)->maxLength(500),
                ])->columns(2),
                Section::make('About / Why choose us')->schema([
                    Toggle::make('show_about_section')
                        ->label('Show About section on homepage')
                        ->default(false),
                    OptimizedImageUpload::make('about_image', 'about', 'photography')
                        ->label('About / workshop image'),
                ])->columns(2),
                Section::make('About content - English')->schema([
                    TextInput::make('about_eyebrow_en')->label('Eyebrow')->maxLength(100),
                    TextInput::make('about_heading_en')->label('Heading')->maxLength(255),
                    Textarea::make('about_description_en')->label('Description')->rows(5)->columnSpanFull(),
                ])->columns(2),
                Section::make('About content - Arabic')->schema([
                    TextInput::make('about_eyebrow_ar')->label('Eyebrow (Arabic)')->maxLength(100),
                    TextInput::make('about_heading_ar')->label('Heading (Arabic)')->maxLength(255),
                    Textarea::make('about_description_ar')->label('Description (Arabic)')->rows(5)->columnSpanFull(),
                ])->columns(2),
                Section::make('Why choose us points')->schema([
                    Repeater::make('about_points')
                        ->label('Trust points')
                        ->schema([
                            TextInput::make('title_en')->label('English title')->required()->maxLength(150),
                            TextInput::make('title_ar')->label('Arabic title')->maxLength(150),
                            Textarea::make('description_en')->label('English description')->rows(2)->maxLength(400),
                            Textarea::make('description_ar')->label('Arabic description')->rows(2)->maxLength(400),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Add trust point')
                        ->reorderable()
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        BusinessSetting::query()->firstOrCreate()->update($this->form->getState());
        app(BusinessSettingsRepository::class)->forget();

        Notification::make()->title('Business settings saved')->success()->send();
    }

}
