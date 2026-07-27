<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_class_id')->constrained('classes');
            $table->foreignId('to_class_id')->nullable()->constrained('classes');
            $table->string('action', 20); // 'promoted' | 'retained' | 'withdrawn'
            $table->string('academic_year')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->constrained('users');
            $table->timestamps();

            $table->index(['student_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
