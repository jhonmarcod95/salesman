<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCropFarmerMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crop_farmer_meeting', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('farmer_meeting_id')->unsigned();
            $table->integer('crop_id')->unsgined();
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
        Schema::dropIfExists('crop_farmer_meeting');
    }
}
