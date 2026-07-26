<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused']);
            $table->foreignId('marked_by')->constrained('users');
            $table->string('remarks')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('class_id');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
