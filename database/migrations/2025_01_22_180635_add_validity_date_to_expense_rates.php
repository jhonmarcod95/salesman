<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValidityDateToExpenseRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_rates', function (Blueprint $table) {
            $table->date('validity_date')->nullable(); // Add validity_date
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_rates', function (Blueprint $table) {
            $table->dropColumn('validity_date');
        });
    }
}
