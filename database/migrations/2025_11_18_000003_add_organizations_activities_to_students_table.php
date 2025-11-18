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
            if (!Schema::hasColumn('students', 'organizations')) {
                $table->text('organizations')->nullable()->after('activities');
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
            if (Schema::hasColumn('students', 'organizations')) {
                $table->dropColumn('organizations');
            }
            if (Schema::hasColumn('students', 'activities')) {
                $table->dropColumn('activities');
            }
        });
    }
};