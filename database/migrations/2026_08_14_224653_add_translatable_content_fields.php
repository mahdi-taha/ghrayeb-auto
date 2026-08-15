<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('short_description_en', 500)->nullable()->after('short_description');
            $table->string('short_description_ar', 500)->nullable()->after('short_description_en');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_ar')->nullable()->after('description_en');
        });

        DB::table('services')->update([
            'name_en' => DB::raw('name'),
            'short_description_en' => DB::raw('short_description'),
            'description_en' => DB::raw('description'),
        ]);

        Schema::table('business_settings', function (Blueprint $table): void {
            $table->string('hero_heading_en')->nullable()->after('hero_heading');
            $table->string('hero_heading_ar')->nullable()->after('hero_heading_en');
            $table->text('hero_description_en')->nullable()->after('hero_description');
            $table->text('hero_description_ar')->nullable()->after('hero_description_en');
            $table->string('primary_cta_text_en')->nullable()->after('primary_cta_text');
            $table->string('primary_cta_text_ar')->nullable()->after('primary_cta_text_en');
            $table->string('secondary_cta_text_en')->nullable()->after('secondary_cta_text');
            $table->string('secondary_cta_text_ar')->nullable()->after('secondary_cta_text_en');
        });

        DB::table('business_settings')->update([
            'hero_heading_en' => DB::raw('hero_heading'),
            'hero_description_en' => DB::raw('hero_description'),
            'primary_cta_text_en' => DB::raw('primary_cta_text'),
            'secondary_cta_text_en' => DB::raw('secondary_cta_text'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn([
                'name_en', 'name_ar',
                'short_description_en', 'short_description_ar',
                'description_en', 'description_ar',
            ]);
        });

        Schema::table('business_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_heading_en', 'hero_heading_ar',
                'hero_description_en', 'hero_description_ar',
                'primary_cta_text_en', 'primary_cta_text_ar',
                'secondary_cta_text_en', 'secondary_cta_text_ar',
            ]);
        });
    }
};
