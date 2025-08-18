<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('leave_requests', function (Blueprint $table) {
        $table->date('from_date')->after('reason');
        $table->date('to_date')->after('from_date');
    });
}

public function down(): void
{
    Schema::table('leave_requests', function (Blueprint $table) {
        $table->dropColumn(['from_date', 'to_date']);
    });
}

};
