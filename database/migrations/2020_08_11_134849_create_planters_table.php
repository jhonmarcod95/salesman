<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlantersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('planter_name');
            $table->string('contact_number');
            $table->string('planter_address');
            $table->string('hacienda_loc');
            $table->double('total_area');
            $table->string('n_p');
            $table->string('r1_r2_r3');
            $table->string('empty');
            $table->string('planter_soil_type_id');
            $table->string('planter_soil_condition_id');
            $table->string('tons_cane');
            $table->string('tons_yields');
            $table->string('assistance_needed');
            $table->string('bir_id')->default('default.jpg');
            $table->string('planter_picture')->default('default.jpg');
            $table->string('parcellary')->default('default.jpg');
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
        Schema::dropIfExists('planters');
    }
}
