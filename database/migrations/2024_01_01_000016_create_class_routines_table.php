<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->tinyInteger('day_of_week');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('teacher_id')->constrained('users');
            $table->timestamps();
            
            $table->index('class_id');
            $table->index('subject_id');
            $table->index('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_routines');
    }
};
