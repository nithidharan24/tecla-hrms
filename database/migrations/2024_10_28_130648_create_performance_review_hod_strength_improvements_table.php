<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewHodStrengthImprovementsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_hod_strength_improvements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('strength')->nullable();
            $table->string('improvement')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_hod_strength_improvements');
    }
}