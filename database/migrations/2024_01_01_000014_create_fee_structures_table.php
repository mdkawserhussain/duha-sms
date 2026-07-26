<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('category');
            $table->decimal('amount', 10, 2);
            $table->string('academic_year');
            $table->timestamps();
            
            $table->index('class_id');
            $table->index('academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
