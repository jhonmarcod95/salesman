<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionalFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('distributor_name')->nullable();
            $table->string('brand_used')->nullable();
            $table->integer('monthly_volume')->unsigned()->default(0);
            $table->date('date_converted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('distributor_name');
            $table->dropColumn('brand_used');
            $table->dropColumn('monthly_volume');
            $table->dropColumn('date_converted');
        });
    }
}
