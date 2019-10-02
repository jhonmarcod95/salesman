<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticularSurveyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('particular_survey', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('particular_id')->unsigned()->index();
            $table->foreign('particular_id')->references('id')->on('particulars')->onDelete('cascade');
            $table->integer('survey_id')->unsigned()->index();
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->integer('ranking')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('particular_survey');
    }
}
