<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAapcActivityTypeIdToAapcFarmerMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_farmer_meetings', function (Blueprint $table) {
            $table->integer('aapc_activity_type_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aapc_farmer_meetings', function (Blueprint $table) {
            $table->dropColumn('aapc_activity_type_id');
        });
    }
}
