<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table): void {
            $table->text('footer_description_en')->nullable();
            $table->text('footer_description_ar')->nullable();
            $table->text('tiktok_url')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table): void {
            $table->dropColumn(['footer_description_en', 'footer_description_ar', 'tiktok_url']);
        });
    }
};
