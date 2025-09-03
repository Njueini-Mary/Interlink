<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('opportunity_skill', function (Blueprint $table) {
            $table->uuid('opportunity_id');
            $table->uuid('skill_id');
            $table->timestamps();

            $table->primary(['opportunity_id', 'skill_id']);
            $table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunity_skill');
    }
};
