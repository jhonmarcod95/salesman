<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerSaleAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_sale_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->char('customer_code',10);
            $table->char('sales_organization',5);
            $table->char('distribution_channel',5);
            $table->char('division',5);
            $table->char('payment_terms',5)->nullable();
            $table->char('order_block',5)->nullable();
            $table->char('delivery_block',5)->nullable();
            $table->char('billing_block',5)->nullable();
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
        Schema::dropIfExists('customer_sale_areas');
    }
}
