<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveTypeIdToLeaveRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'leave_type_id')) {
                 $table->foreignId('leave_type_id')->constrained('leave_types')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasColumn('leave_requests', 'leave_type_id')) {
                $table->dropForeign(['leave_type_id']);
                $table->dropColumn('leave_type_id');
            }
        });
    }
}