<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the organization_student pivot table for org-intern relationships.
     */
    public function up()
    {
        Schema::create('organization_student', function (Blueprint $table) {
            $table->uuid('organization_id');
            $table->uuid('student_id');
            $table->timestamps();
            $table->primary(['organization_id', 'student_id']);
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('organization_student');
    }
};
