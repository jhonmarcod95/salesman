<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyExpensesEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses_entries', function (Blueprint $table) {
            $table->dropForeign('expenses_entries_expense_id_foreign');
            $table->dropColumn('expense_id');
            $table->integer('user_id')->unsigned();
            $table->string('expenses', 1000);
            $table->double('totalExpenses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses_entries');
    }
}
