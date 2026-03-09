<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('travel_report_approvals', function (Blueprint $table) {
            $table->foreignId('approval_step_id')
                ->nullable()
                ->after('step_order')
                ->constrained('approval_steps')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('travel_report_approvals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approval_step_id');
        });
    }
};
