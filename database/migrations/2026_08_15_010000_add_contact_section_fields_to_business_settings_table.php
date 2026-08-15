<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table): void {
            $table->boolean('show_contact_section')->default(true);
            $table->string('contact_eyebrow_en')->nullable();
            $table->string('contact_eyebrow_ar')->nullable();
            $table->string('contact_heading_en')->nullable();
            $table->string('contact_heading_ar')->nullable();
            $table->text('contact_description_en')->nullable();
            $table->text('contact_description_ar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'show_contact_section',
                'contact_eyebrow_en',
                'contact_eyebrow_ar',
                'contact_heading_en',
                'contact_heading_ar',
                'contact_description_en',
                'contact_description_ar',
            ]);
        });
    }
};
