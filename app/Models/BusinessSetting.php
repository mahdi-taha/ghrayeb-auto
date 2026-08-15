<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'show_about_section' => 'boolean',
            'show_contact_section' => 'boolean',
            'about_points' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (BusinessSetting $settings): void {
            foreach (['hero_heading', 'hero_description', 'primary_cta_text', 'secondary_cta_text'] as $field) {
                $englishValue = $settings->getAttribute("{$field}_en");

                if (array_key_exists("{$field}_en", $settings->getAttributes())) {
                    $settings->setAttribute($field, $englishValue);
                }
            }
        });
    }
}
