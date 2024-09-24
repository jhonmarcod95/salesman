<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeWeeklyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_weekly_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_monthly_expense_id')->unsigned();
            $table->integer('week_no');
            $table->integer('user_id')->unsigned();
            $table->integer('expense_count')->nullable();
            $table->integer('verified_count')->nullable();
            $table->integer('unverified_count')->nullable();
            $table->integer('rejected_count')->nullable();
            $table->integer('expense_amount')->nullable();
            $table->integer('verified_amount')->nullable();
            $table->integer('rejected_amount')->nullable();
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
        Schema::dropIfExists('employee_weekly_expenses');
    }
}
