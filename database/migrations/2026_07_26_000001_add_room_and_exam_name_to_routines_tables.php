<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_routines', function (Blueprint $table) {
            $table->string('room')->nullable()->after('teacher_id');
            $table->foreignId('room_id')->nullable()->after('room')->constrained('rooms');
        });

        Schema::table('exam_routines', function (Blueprint $table) {
            $table->string('exam_name')->nullable()->after('subject_id');
            $table->string('room')->nullable()->after('end_time');
            $table->foreignId('room_id')->nullable()->after('room')->constrained('rooms');
        });
    }

    public function down(): void
    {
        Schema::table('class_routines', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room', 'room_id']);
        });

        Schema::table('exam_routines', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['exam_name', 'room', 'room_id']);
        });
    }
};
