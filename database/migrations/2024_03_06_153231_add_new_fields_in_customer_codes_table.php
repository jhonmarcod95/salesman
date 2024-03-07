<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsInCustomerCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_codes', function (Blueprint $table) {
            $table->char('name2',50)->nullable();
            $table->char('name3',50)->nullable();
            $table->char('name4',50)->nullable();
            $table->char('street4',60)->nullable();
            $table->char('street5',60)->nullable();
            $table->char('postal_code',10)->nullable();
            $table->char('region',4)->nullable();
            $table->char('country',4)->nullable();
            $table->char('county',50)->nullable();
            $table->char('township',50)->nullable();
            
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
