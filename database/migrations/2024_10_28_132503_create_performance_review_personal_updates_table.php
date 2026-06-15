<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewPersonalUpdatesTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_personal_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('married_last_year')->nullable();
            $table->text('married_last_year_details')->nullable();
            $table->string('marriage_plans')->nullable();
            $table->text('marriage_plans_details')->nullable();
            $table->string('studies_last_year')->nullable();
            $table->text('studies_last_year_details')->nullable();
            $table->string('study_plans')->nullable();
            $table->text('study_plans_details')->nullable();
            $table->string('health_issues_last_year')->nullable();
            $table->text('health_issues_last_year_details')->nullable();
            $table->string('certification_plans')->nullable();
            $table->text('certification_plans_details')->nullable();
            $table->string('others_last_year')->nullable();
            $table->text('others_last_year_details')->nullable();
            $table->string('others_current_year')->nullable();
            $table->text('others_current_year_details')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_personal_updates');
    }
}