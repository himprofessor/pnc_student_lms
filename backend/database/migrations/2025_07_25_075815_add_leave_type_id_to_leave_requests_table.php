<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveTypeIdToLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Add leave_type_id if it does not exist
            if (!Schema::hasColumn('leave_requests', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
            }

            // Add contact_info if it does not exist
            if (!Schema::hasColumn('leave_requests', 'contact_info')) {
                $table->string('contact_info')->nullable()->after('reason');
            }

            // Add supporting_documents if it does not exist
            if (!Schema::hasColumn('leave_requests', 'supporting_documents')) {
                $table->string('supporting_documents')->nullable()->after('contact_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Drop foreign key and column for leave_type_id if exists
            if (Schema::hasColumn('leave_requests', 'leave_type_id')) {
                $table->dropForeign(['leave_type_id']);
                $table->dropColumn('leave_type_id');
            }

            // Drop contact_info if exists
            if (Schema::hasColumn('leave_requests', 'contact_info')) {
                $table->dropColumn('contact_info');
            }

            // Drop supporting_documents if exists
            if (Schema::hasColumn('leave_requests', 'supporting_documents')) {
                $table->dropColumn('supporting_documents');
            }
        });
    }
}
