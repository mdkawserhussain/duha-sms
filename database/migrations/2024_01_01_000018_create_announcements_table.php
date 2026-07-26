<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('type', ['school_wide', 'class_level']);
            $table->foreignId('class_id')->nullable()->constrained('classes');
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('class_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
