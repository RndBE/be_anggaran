<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add approval_status to travel_reports
        Schema::table('travel_reports', function (Blueprint $table) {
            $table->string('approval_status')->default('draft')->after('status');
            // draft, in_review, approved, rejected
        });

        // Dedicated approval table for LHP
        Schema::create('travel_report_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_report_id')->constrained()->onDelete('cascade');
            $table->string('step'); // level3, level2, k3, hrd, finance
            $table->integer('step_order'); // 1-5
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_report_approvals');
        Schema::table('travel_reports', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
