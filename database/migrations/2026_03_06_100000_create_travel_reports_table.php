<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('surat_tugas_no')->nullable();
            $table->date('surat_tugas_date')->nullable();
            $table->string('destination_city');
            $table->date('departure_date');
            $table->date('return_date');
            $table->text('purpose'); // Maksud & Tujuan
            $table->json('results')->nullable(); // Hasil yang dicapai (array)
            $table->text('issues')->nullable(); // Permasalahan & Kendala
            $table->text('conclusion')->nullable(); // Kesimpulan
            $table->json('recommendations')->nullable(); // Rekomendasi (array)
            $table->string('status')->default('draft'); // draft, submitted, approved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_reports');
    }
};
