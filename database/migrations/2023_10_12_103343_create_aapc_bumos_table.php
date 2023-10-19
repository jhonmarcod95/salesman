<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAapcBumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aapc_bumos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bumos_type_id')->unsigned();
            $table->integer('insect_type_id')->unsigned();
            $table->string('insect_brand_name')->nullable();
            $table->integer('disease_type_id')->unsigned();
            $table->string('disease_brand_name');
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
        Schema::dropIfExists('aapc_bumos');
    }
}
