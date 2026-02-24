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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('request_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // strok_asli, foto_lokasi, lpj, e_toll, invoice_ojol, surat_tugas
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
