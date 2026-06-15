<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewProfessionalGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_professional_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->text('goal_self');
            $table->text('goal_ro')->nullable();
            $table->text('goal_hod')->nullable();
            $table->boolean('is_last_year');
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_professional_goals');
    }
}