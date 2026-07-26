<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('exam_routine_id')->constrained('exam_routines')->cascadeOnDelete();
            $table->decimal('marks', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('evaluated_by')->constrained('users');
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('subject_id');
            $table->index('exam_routine_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
