<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add per-activity fields: results, issues, conclusion
        Schema::table('travel_report_activities', function (Blueprint $table) {
            $table->json('results')->nullable()->after('description')
                ->comment('Array of results for this activity');
            $table->text('issues')->nullable()->after('results')
                ->comment('Issues/problems for this activity');
            $table->text('conclusion')->nullable()->after('issues')
                ->comment('Conclusion for this activity');
        });

        // Link documents to specific activities
        Schema::table('travel_report_documents', function (Blueprint $table) {
            $table->foreignId('travel_report_activity_id')->nullable()->after('travel_report_id')
                ->constrained('travel_report_activities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('travel_report_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('travel_report_activity_id');
        });

        Schema::table('travel_report_activities', function (Blueprint $table) {
            $table->dropColumn(['results', 'issues', 'conclusion']);
        });
    }
};
