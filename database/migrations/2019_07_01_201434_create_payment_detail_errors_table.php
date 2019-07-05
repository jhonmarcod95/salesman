<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDetailErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_detail_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_header_error_id');
            $table->string('return_message_type');
            $table->string('return_message_id');
            $table->string('return_message_number');
            $table->string('return_message_description');
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
        Schema::dropIfExists('payment_detail_errors');
    }
}
