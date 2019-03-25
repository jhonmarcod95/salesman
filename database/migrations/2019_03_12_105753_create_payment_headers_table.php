<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code');
            $table->string('company_name');
            $table->string('reference_number');
            $table->string('ap_user');
            $table->string('vendor_code');
            $table->string('vendor_name');
            $table->string('document_type');
            $table->string('payment_terms');
            $table->string('header_text');
            $table->date('document_date');
            $table->date('posting_date');
            $table->date('baseline_date');
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
        Schema::dropIfExists('payment_headers');
    }
}
