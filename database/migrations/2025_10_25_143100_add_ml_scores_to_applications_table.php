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
            $table->float('tfidf_score')->nullable()->after('match_score');
            $table->float('logreg_score')->nullable()->after('tfidf_score');
            $table->float('rf_score')->nullable()->after('logreg_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['tfidf_score', 'logreg_score', 'rf_score']);
        });
    }
};
