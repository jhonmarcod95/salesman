<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPlanterHaciendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planter_haciendas', function (Blueprint $table) {
            $table->string('planter_code');
            $table->string('name');
            $table->string('mobile_number');
            $table->string('hacienda_code');
            $table->string('planter_audit_no');
            $table->string('address');
            $table->string('area');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planter_haciendas', function (Blueprint $table) {
            //
        });
    }
}
