<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAapcCultivatedCropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aapc_cultivated_crops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aapc_farmer_id')->unsigned();
            $table->string('crop_name');
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
        Schema::dropIfExists('aapc_cultivated_crops');
    }
}
