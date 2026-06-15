<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewSpecialInitiativesTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_special_initiatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->text('achievement_self');
            $table->text('achievement_ro')->nullable();
            $table->text('achievement_hod')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_special_initiatives');
    }
}