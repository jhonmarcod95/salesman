<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_deductions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('expense_id')->unsigned();
            $table->bigInteger('employee_monthly_expense_id')->unsigned();
            $table->decimal('balance_from_amount', 15, 2);
            $table->decimal('balance_to_amount', 15, 2);
            $table->decimal('balance_deducted_amount', 15, 2);
            $table->decimal('expense_from_amount', 15, 2);
            $table->decimal('expense_to_amount', 15, 2);
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
        Schema::dropIfExists('deductions');
    }
}
