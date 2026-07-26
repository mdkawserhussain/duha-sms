<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diary_entry_id')->constrained('diary_entries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment');
            $table->timestamps();
            
            $table->index('diary_entry_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_comments');
    }
};
