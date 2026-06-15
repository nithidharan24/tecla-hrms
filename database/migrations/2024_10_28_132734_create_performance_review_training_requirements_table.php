<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewTrainingRequirementsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_training_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->text('training_self');
            $table->text('training_ro')->nullable();
            $table->text('training_hod')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_training_requirements');
    }
}