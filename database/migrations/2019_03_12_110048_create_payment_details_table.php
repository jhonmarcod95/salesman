<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item');
            $table->string('item_text')->nullable();
            $table->integer('gl_account');
            $table->string('description');
            $table->string('assignment')->nullable();
            $table->string('input_tax_code')->nullable();
            $table->string('internal_order')->nullable();
            $table->integer('amount');
            $table->string('charge_type')->nullable();
            $table->string('business_area');
            $table->string('or_number')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_tin_number')->nullable();
            $table->string('payment_header_id');
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
        Schema::dropIfExists('payment_details');
    }
}
