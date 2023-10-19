<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAapcFarmerMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aapc_farmer_meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('region_id')->unsigned();
            $table->string('city')->nullable();
            $table->string('venue')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('date_conducted');
            $table->integer('store_id')->unsigned();
            $table->integer('bumo_id')->unsigned();
            $table->integer('farmer_id')->unsigned();
            $table->integer('vegestable_id')->unsigned();
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
        Schema::dropIfExists('aapc_farmer_meetings');
    }
}
