<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the students table for student profiles.
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('university')->nullable();
            $table->string('course')->nullable();
            $table->string('year')->nullable();
            $table->string('resume')->nullable();
            $table->string('academic_field')->nullable();
            $table->boolean('is_available')->default(true);
            $table->float('rating')->default(0);
            $table->integer('rating_count')->default(0);
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
