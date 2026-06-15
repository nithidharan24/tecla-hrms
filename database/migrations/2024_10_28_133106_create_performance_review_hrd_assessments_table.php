<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewHrdAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_hrd_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->decimal('kra_points_available', 5, 2);
            $table->decimal('kra_points_scored', 5, 2);
            $table->text('kra_comment')->nullable();
            $table->decimal('professional_points_available', 5, 2);
            $table->decimal('professional_points_scored', 5, 2);
            $table->text('professional_comment')->nullable();
            $table->decimal('personal_points_available', 5, 2);
            $table->decimal('personal_points_scored', 5, 2);
            $table->text('personal_comment')->nullable();
            $table->decimal('achievement_points_available', 5, 2);
            $table->decimal('achievement_points_scored', 5, 2);
            $table->text('achievement_comment')->nullable();
            $table->decimal('total_points_available', 5, 2);
            $table->decimal('total_points_scored', 5, 2);
            $table->text('total_comment')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_hrd_assessments');
    }
}