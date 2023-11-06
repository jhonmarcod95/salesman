<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAapcRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_regions', function (Blueprint $table) {
            $table->string('country_code')->nullabe();
            $table->string('code')->nullabe();
            $table->string('sap_server')->nullabe();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aapc_regions', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('code');
            $table->dropColumn('sap_server');
        });
    }
}
