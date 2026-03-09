<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('approval_flows', function (Blueprint $table) {
            $table->string('flow_type')->default('request')->after('name');
            // 'request' = flow for budget/reimbursement requests
            // 'lhp'     = flow for travel report (LHP) approvals
        });
    }

    public function down(): void
    {
        Schema::table('approval_flows', function (Blueprint $table) {
            $table->dropColumn('flow_type');
        });
    }
};
