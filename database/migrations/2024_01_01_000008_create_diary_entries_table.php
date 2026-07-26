<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('teacher_id')->constrained('users');
            $table->text('activities')->nullable();
            $table->text('meals')->nullable();
            $table->text('behavior')->nullable();
            $table->text('homework')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('class_id');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
