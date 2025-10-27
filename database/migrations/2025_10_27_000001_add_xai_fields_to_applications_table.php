<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->json('matched_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->text('match_message')->nullable();
            $table->string('xai_course')->nullable();
            $table->string('xai_academic_field')->nullable();
            $table->string('xai_gpa')->nullable();
            $table->text('xai_interests')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'matched_skills',
                'missing_skills',
                'match_message',
                'xai_course',
                'xai_academic_field',
                'xai_gpa',
                'xai_interests',
            ]);
        });
    }
};
