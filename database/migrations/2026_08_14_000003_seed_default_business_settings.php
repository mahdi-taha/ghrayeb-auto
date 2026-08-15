<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('business_settings')->exists()) {
            DB::table('business_settings')->insert([
                ...Arr::only(config('business.defaults'), Schema::getColumnListing('business_settings')),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('business_settings')->truncate();
    }
};
