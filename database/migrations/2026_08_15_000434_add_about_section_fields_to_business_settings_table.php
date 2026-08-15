<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->boolean('show_about_section')->default(false)->after('secondary_cta_text_ar');
            $table->string('about_eyebrow_en')->nullable()->after('show_about_section');
            $table->string('about_eyebrow_ar')->nullable()->after('about_eyebrow_en');
            $table->string('about_heading_en')->nullable()->after('about_eyebrow_ar');
            $table->string('about_heading_ar')->nullable()->after('about_heading_en');
            $table->text('about_description_en')->nullable()->after('about_heading_ar');
            $table->text('about_description_ar')->nullable()->after('about_description_en');
            $table->string('about_image')->nullable()->after('about_description_ar');
            $table->json('about_points')->nullable()->after('about_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_about_section',
                'about_eyebrow_en',
                'about_eyebrow_ar',
                'about_heading_en',
                'about_heading_ar',
                'about_description_en',
                'about_description_ar',
                'about_image',
                'about_points',
            ]);
        });
    }
};
