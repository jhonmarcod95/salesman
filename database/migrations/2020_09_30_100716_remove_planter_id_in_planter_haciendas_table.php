<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePlanterIdInPlanterHaciendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planter_haciendas', function (Blueprint $table) {
            $table->dropColumn('planter_id');
        });
    }
}
