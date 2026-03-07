<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('surat_tugas_urut')->nullable()->after('rejection_reason');
            $table->date('surat_tugas_date')->nullable()->after('surat_tugas_urut');
            $table->string('surat_tugas_no')->nullable()->after('surat_tugas_date');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['surat_tugas_urut', 'surat_tugas_date', 'surat_tugas_no']);
        });
    }
};
