<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Generation 2023
            $table->year('year')->nullable(); // Optional: store year of generation
            $table->timestamps();
        });

        // Add generation_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('generation_id')
                ->nullable()
                ->after('role_id')
                ->constrained('generations')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['generation_id']);
            $table->dropColumn('generation_id');
        });

        Schema::dropIfExists('generations');
    }
};
