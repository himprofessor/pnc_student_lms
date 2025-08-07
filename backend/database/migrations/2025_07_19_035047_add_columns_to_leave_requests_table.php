<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'leave_type')) {
                $table->string('leave_type')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('leave_requests', 'contact_info')) {
                $table->string('contact_info')->nullable()->after('leave_type');
            }

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
            if (Schema::hasColumn('leave_requests', 'leave_type')) {
                $table->dropColumn('leave_type');
            }

            if (Schema::hasColumn('leave_requests', 'contact_info')) {
                $table->dropColumn('contact_info');
            }

            if (Schema::hasColumn('leave_requests', 'supporting_documents')) {
                $table->dropColumn('supporting_documents');
            }
        });
    }
};
