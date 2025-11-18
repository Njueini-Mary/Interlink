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
            // Rename stipend to monthly_stipend if exists
            if (Schema::hasColumn('opportunities', 'stipend')) {
                $table->renameColumn('stipend', 'monthly_stipend');
            }
            // Add compensation_type
            if (!Schema::hasColumn('opportunities', 'compensation_type')) {
                $table->enum('compensation_type', ['monthly', 'hourly', 'stipend'])->default('monthly')->after('status');
            }
            // Add hourly rate fields
            if (!Schema::hasColumn('opportunities', 'min_hourly_rate')) {
                $table->decimal('min_hourly_rate', 12, 2)->nullable()->after('monthly_stipend');
            }
            if (!Schema::hasColumn('opportunities', 'max_hourly_rate')) {
                $table->decimal('max_hourly_rate', 12, 2)->nullable()->after('min_hourly_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Rename monthly_stipend back to stipend
            if (Schema::hasColumn('opportunities', 'monthly_stipend')) {
                $table->renameColumn('monthly_stipend', 'stipend');
            }
            // Drop compensation_type
            if (Schema::hasColumn('opportunities', 'compensation_type')) {
                $table->dropColumn('compensation_type');
            }
            // Drop hourly rate fields
            if (Schema::hasColumn('opportunities', 'min_hourly_rate')) {
                $table->dropColumn('min_hourly_rate');
            }
            if (Schema::hasColumn('opportunities', 'max_hourly_rate')) {
                $table->dropColumn('max_hourly_rate');
            }
        });
    }
};
