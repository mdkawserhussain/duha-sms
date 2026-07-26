<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['paid', 'due', 'partial'])->default('due');
            $table->date('paid_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('fee_structure_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_records');
    }
};
