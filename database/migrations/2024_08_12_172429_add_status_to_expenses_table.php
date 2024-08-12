<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('status_id')->default(0);
            $table->date('expense_from')->nullable();
            $table->date('expense_to')->nullable();
            $table->date('should_be_posting_date')->nullable();
            $table->text('expense_grouping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('status_id');
            $table->dropColumn('expense_from');
            $table->dropColumn('expense_to');
            $table->dropColumn('should_be_posting_date');
            $table->dropColumn('expense_grouping');
        });
    }
}
