<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            // Untuk item tipe lumpsum: simpan referensi zona + jumlah orang + hari
            $table->foreignId('travel_zone_id')->nullable()->after('description')
                ->constrained('travel_zones')->nullOnDelete();
            $table->unsignedSmallInteger('person_count')->nullable()->after('travel_zone_id')
                ->comment('Jumlah orang yang ikut dinas');
            $table->unsignedSmallInteger('day_count')->nullable()->after('person_count')
                ->comment('Jumlah hari dinas');
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropForeign(['travel_zone_id']);
            $table->dropColumn(['travel_zone_id', 'person_count', 'day_count']);
        });
    }
};
