<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameGrassrotsExpenseTypeIdInGrassrootsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grassroots', function (Blueprint $table) {
            $table->renameColumn('grassrots_expense_type_id', 'grassroots_expense_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grassroots', function (Blueprint $table) {
            //
        });
    }
}
