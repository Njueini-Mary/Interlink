<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->string('industry')->nullable()->after('description');
            $table->float('min_gpa')->nullable()->after('industry');
            $table->string('course')->nullable()->after('min_gpa');
            $table->string('academic_field')->nullable()->after('course');
            $table->string('year')->nullable()->after('academic_field');
            $table->string('university')->nullable()->after('year');
            $table->string('organizations')->nullable()->after('university');
            $table->text('interests')->nullable()->after('organizations');
            $table->text('activities')->nullable()->after('interests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'industry',
                'min_gpa',
                'course',
                'academic_field',
                'year',
                'university',
                'organizations',
                'interests',
                'activities',
            ]);
        });
    }
};
