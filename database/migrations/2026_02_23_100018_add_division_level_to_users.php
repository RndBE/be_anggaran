<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // division_id column may already exist (from a partially-failed previous run)
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'division_id')) {
            \Illuminate\Support\Facades\Schema::table('users', function (Blueprint $table) {
                $table->foreignId('division_id')->nullable()->after('id')->constrained('divisions')->nullOnDelete();
            });
        } else {
            // Column exists but FK may be missing — add just the FK
            \Illuminate\Support\Facades\Schema::table('users', function (Blueprint $table) {
                $table->foreign('division_id')->references('id')->on('divisions')->nullOnDelete();
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'level')) {
            \Illuminate\Support\Facades\Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('level')->nullable()->after('division_id')->comment('Jabatan level: 1=lowest, 4=highest');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn('level');
        });
    }
};
