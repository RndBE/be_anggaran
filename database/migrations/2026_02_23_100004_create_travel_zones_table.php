<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_zones', function (Blueprint $table) {
            $table->id();
            $table->integer('zone'); // 1, 2, 3, 4
            $table->string('name'); // e.g. Zone 1
            $table->decimal('meal_allowance', 15, 2); // nominal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_zones');
    }
};
