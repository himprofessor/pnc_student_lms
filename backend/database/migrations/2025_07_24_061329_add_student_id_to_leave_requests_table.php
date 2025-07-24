<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToLeaveRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id')->nullable()->after('id'); // Adjust 'after' based on your needs
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('student_id');
        });
    }
}