<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerformanceReviewBasicInfosTable extends Migration
{
    public function up()
    {
        Schema::create('performance_review_basic_infos', function (Blueprint $table) {
            $table->id();
            $table->Integer('employee_name');
            $table->string('employee_id');
            $table->Integer('designation_id');
            $table->Integer('department_id');
            $table->date('date_of_join');
            $table->string('ro_name');
            $table->string('ro_designation');
            $table->timestamps();
            $table->boolean('deleted_at')->default(0);
            $table->string('status')->default('active');

            $table->foreign('employee_name')->references('id')->on('allemployees');
            $table->foreign('designation_id')->references('id')->on('designation');
            $table->foreign('department_id')->references('id')->on('department');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_basic_infos');
    }
}