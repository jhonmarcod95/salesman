<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFarmerRegionNameToAapcFarmersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_farmers', function (Blueprint $table) {
            $table->string('region_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aapc_farmers', function (Blueprint $table) {
            $table->dropColumn('region_name');
        });
    }
}
