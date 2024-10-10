<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeMonthlyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_monthly_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('month');
            $table->string('year');
            $table->integer('expense_count')->nullable();
            $table->integer('verified_count')->nullable();
            $table->integer('unverified_count')->nullable();
            $table->integer('rejected_count')->nullable();
            $table->double('expense_amount', 8, 2)->nullable();
            $table->double('verified_amount', 8, 2)->nullable();
            $table->double('rejected_amount', 8, 2)->nullable();
            $table->double('balance_rejected_amount', 8, 2)->nullable();
            $table->dateTime('date_notified')->nullable();
            $table->boolean('is_acknowledge')->default(false)->nullable();
            $table->dateTime('acknowledge_date')->nullable();
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
        Schema::dropIfExists('employee_monthly_expenses');
    }
}
