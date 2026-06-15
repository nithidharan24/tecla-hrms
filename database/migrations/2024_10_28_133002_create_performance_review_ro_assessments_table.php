<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewRoAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_ro_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('work_issues');
            $table->text('work_issues_details')->nullable();
            $table->string('leave_issues');
            $table->text('leave_issues_details')->nullable();
            $table->string('stability_issues');
            $table->text('stability_issues_details')->nullable();
            $table->string('attitude_issues');
            $table->text('attitude_issues_details')->nullable();
            $table->string('other_issues');
            $table->text('other_issues_details')->nullable();
            $table->string('overall_performance');
            $table->text('overall_performance_details')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_ro_assessments');
    }
}