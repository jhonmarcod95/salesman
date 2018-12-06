<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('type')->unsigned();
            $table->string('code');
            $table->string('name');
            $table->string('address');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('status')->default(2);
            $table->string('remarks')->nullable();
            $table->boolean('isCurrent')->default(0);
            $table->boolean('isApproved')->default(0);
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
        Schema::dropIfExists('request_schedules');
    }
}
