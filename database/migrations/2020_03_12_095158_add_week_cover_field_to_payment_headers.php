<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeekCoverFieldToPaymentHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_headers', function (Blueprint $table) {
            $table->date('expense_from')->nullable();
            $table->date('expense_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_headers', function (Blueprint $table) {
            $table->dropColumn('expense_from');
            $table->dropColumn('expense_to');
        });
    }
}
