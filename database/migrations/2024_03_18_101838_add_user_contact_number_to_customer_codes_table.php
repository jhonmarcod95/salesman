<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserContactNumberToCustomerCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_codes', function (Blueprint $table) {
            $table->char('telephone_1',30)->nullable();
            $table->char('telephone_2',30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_codes', function (Blueprint $table) {
            //
        });
    }
}
