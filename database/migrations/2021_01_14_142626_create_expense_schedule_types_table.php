<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseScheduleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_schedule_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salesman_id')->unsigned();
            $table->text('default_expense_types');
            $table->text('expense_hide');
            $table->text('expense_display');
            $table->text('scheduletype_condition');
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
        Schema::dropIfExists('expense_schedule_types');
    }
}
