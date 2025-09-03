<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the student_category pivot table for student-category relationships.
     */
    public function up()
    {
        Schema::create('student_category', function (Blueprint $table) {
            $table->uuid('student_id');
            $table->uuid('category_id');
            $table->primary(['student_id', 'category_id']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('student_category');
    }
};
