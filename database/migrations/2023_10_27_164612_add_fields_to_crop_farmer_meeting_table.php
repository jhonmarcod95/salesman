<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToCropFarmerMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crop_farmer_meeting', function (Blueprint $table) {
            $table->date('plant_season_start')->nullable();
            $table->date('plant_season_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crop_farmer_meeting', function (Blueprint $table) {
            $table->dropColumn('plant_season_start');
            $table->dropColumn('plant_season_end');
        });
    }
}
