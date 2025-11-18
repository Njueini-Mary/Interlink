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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'gpa')) {
                $table->float('gpa')->nullable()->after('academic_field');
            }
            if (!Schema::hasColumn('students', 'interests')) {
                $table->text('interests')->nullable()->after('gpa');
            }
            if (!Schema::hasColumn('students', 'activities')) {
                $table->text('activities')->nullable()->after('interests');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'interests', 'activities']);
        });
    }
};
