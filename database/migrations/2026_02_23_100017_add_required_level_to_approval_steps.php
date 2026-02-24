<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('approval_steps', function (Blueprint $table) {
            // Make role_id nullable to support division-level steps
            $table->foreignId('role_id')->nullable()->change();
            // New: represents "level X user from the requester's division must approve"
            $table->unsignedTinyInteger('required_level')->nullable()->after('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('approval_steps', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable(false)->change();
            $table->dropColumn('required_level');
        });
    }
};
