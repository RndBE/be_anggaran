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
        Schema::create('client_codes', function (Blueprint $table) {
            $table->id();
            $table->string('prefix'); // e.g. GOV1, GOV2, POE
            $table->string('instansi_singkat'); // e.g. BBWS, BRANTAS
            $table->integer('counter')->default(0);
            $table->string('name')->nullable(); // Real name of client (sensitive)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_codes');
    }
};
