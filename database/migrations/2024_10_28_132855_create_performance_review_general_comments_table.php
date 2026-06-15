<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewGeneralCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_general_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->text('comment_self');
            $table->text('comment_ro')->nullable();
            $table->text('comment_hod')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_general_comments');
    }
}