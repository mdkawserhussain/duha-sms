<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Admission-specific fields
            $table->string('child_name')->nullable()->after('body');
            $table->date('child_dob')->nullable()->after('child_name');
            $table->string('child_gender', 10)->nullable()->after('child_dob');
            $table->string('previous_school')->nullable()->after('child_gender');
            $table->text('documents')->nullable()->after('previous_school'); // JSON array of document paths
            $table->string('photo')->nullable()->after('documents');
            $table->foreignId('class_id')->nullable()->after('photo')->constrained('classes');
            $table->text('guardian_info')->nullable()->after('class_id'); // JSON: guardian name, phone, email, relationship
            $table->text('additional_notes')->nullable()->after('guardian_info');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'child_name', 'child_dob', 'child_gender', 'previous_school',
                'documents', 'photo', 'class_id', 'guardian_info', 'additional_notes',
            ]);
        });
    }
};
