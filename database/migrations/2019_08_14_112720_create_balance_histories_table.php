<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('internal_order');
            $table->date('date');
            $table->integer('payment_detail_id');
            $table->integer('from')->nullable();
            $table->integer('to');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_histories');
    }
}
