<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAapcBumoHerbicidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aapc_bumo_herbicides', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bumos_type_id')->unsigned();
            $table->integer('aapc_farmer_meeting_id')->unsigned();
            $table->string('weeds_brand_name');
            $table->integer('aapc_herbicide_type_id')->unsigned();
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
        Schema::dropIfExists('aapc_bumo_herbicides');
    }
}
