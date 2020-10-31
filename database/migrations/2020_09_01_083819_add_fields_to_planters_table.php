<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPlantersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planters', function (Blueprint $table) {
            $table->text('crop_tech_remarks')->nullable();
            $table->integer('area_converted')->unsigned();
            $table->string('variety')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planters', function (Blueprint $table) {
            $table->dropColumn('crop_tech_remarks');
            $table->dropColumn('area_converted');
            $table->dropColumn('variety');
        });
    }
}
