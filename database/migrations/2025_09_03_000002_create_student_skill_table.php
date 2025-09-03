<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the student_skill pivot table for student-skill relationships.
     */
    public function up()
    {
        Schema::create('student_skill', function (Blueprint $table) {
            $table->uuid('student_id');
            $table->uuid('skill_id');
            $table->primary(['student_id', 'skill_id']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_skill');
    }
};
