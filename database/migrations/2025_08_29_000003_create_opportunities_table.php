<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Creates the opportunities table for internship/attachment/volunteering listings.
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->uuid('category_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->enum('type', ['WBL', 'SBL']);
            $table->date('deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'filled'])->default('open');
            $table->enum('compensation_type', ['monthly', 'hourly', 'stipend'])->default('monthly');
            $table->string('currency', 8)->nullable();
            $table->decimal('monthly_stipend', 12, 2)->nullable();
            $table->decimal('min_hourly_rate', 12, 2)->nullable();
            $table->decimal('max_hourly_rate', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('opportunities');
    }
};
