<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_review_professional_excellences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('key_result_area')->nullable();
            $table->string('key_performance_indicator')->nullable();
            $table->decimal('weightage', 5, 2)->default(0);
            $table->decimal('percentage_self', 5, 2)->default(0);
            $table->decimal('points_self', 5, 2)->default(0);
            $table->decimal('percentage_ro', 5, 2)->default(0);
            $table->decimal('points_ro', 5, 2)->default(0);
            $table->decimal('total_percentage_self', 5, 2)->default(0);
            $table->decimal('total_percentage_ro', 5, 2)->default(0);
            $table->decimal('total_points_self', 5, 2)->default(0);
            $table->decimal('total_points_ro', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('review_id')
                  ->references('id')
                  ->on('performance_review_basic_infos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_professional_excellences');
    }
};
