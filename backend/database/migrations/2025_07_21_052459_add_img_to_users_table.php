<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImgToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This method adds a new nullable string column `img` to the `users` table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('img')->nullable()->after('email'); // Add after email for clarity
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method removes the `img` column if rolled back.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('img');
        });
    }
}
