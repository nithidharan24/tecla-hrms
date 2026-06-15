<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewPersonalGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_personal_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('last_year_goal')->nullable();
            $table->string('current_year_goal')->nullable();
            $table->timestamps();

            

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_personal_goals');
    }
}