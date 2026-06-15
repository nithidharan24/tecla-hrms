<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewSignaturesTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('employee_name');
            $table->string('employee_signature');
            $table->date('employee_date');
            $table->string('ro_name');
            $table->string('ro_signature');
            $table->date('ro_date');
            $table->string('hod_name');
            $table->string('hod_signature');
            $table->date('hod_date');
            $table->string('hrd_name');
            $table->string('hrd_signature');
            $table->date('hrd_date');
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('performance_review_basic_infos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_signatures');
    }
}