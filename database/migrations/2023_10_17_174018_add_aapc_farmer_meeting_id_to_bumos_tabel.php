<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAapcFarmerMeetingIdToBumosTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_bumos', function (Blueprint $table) {
            $table->integer('aapc_farmer_meeting_id')->unsiged();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aapc_bumos', function (Blueprint $table) {
            $table->dropColumn('aapc_farmer_meeting_id');
        });
    }
}
