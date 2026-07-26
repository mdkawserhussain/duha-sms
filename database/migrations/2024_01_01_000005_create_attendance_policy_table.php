<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_policy', function (Blueprint $table) {
            $table->id();
            $table->json('policy_data');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_policy');
    }
};
