<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('class_educator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educator_id')->constrained('educators')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('student_classes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_educator');
    }
};
