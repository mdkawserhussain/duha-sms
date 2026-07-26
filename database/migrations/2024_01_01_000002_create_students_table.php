<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->date('dob');
            $table->enum('gender', ['m', 'f', 'other']);
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('admission_date');
            $table->string('admission_no')->unique();
            $table->string('blood_group')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'withdrawn'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('guardian_id');
            $table->index('class_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
