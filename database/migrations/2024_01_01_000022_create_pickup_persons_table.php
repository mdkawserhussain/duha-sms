<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->boolean('is_authorized')->default(true);
            $table->timestamps();
            
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_persons');
    }
};
