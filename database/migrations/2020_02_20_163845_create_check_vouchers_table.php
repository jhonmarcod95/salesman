<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code');
            $table->date('document_date');
            $table->date('posting_date');
            $table->string('reference_number');
            $table->string('header_text');
            $table->string('bank_account');
            $table->double('amount', 8, 2);
            $table->string('business_area');
            $table->string('vendor_code');
            $table->string('document_code');
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
        Schema::dropIfExists('check_vouchers');
    }
}
