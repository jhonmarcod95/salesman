<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactNumberToAapcStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_stores', function (Blueprint $table) {
            $table->string('contact_number')->nullable();
            // $table->text('city')->nullable();
            // $table->integer('region_id')->unsigned();
            $table->string('barangay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aapc_stores', function (Blueprint $table) {
            $table->dropColumn('contact_number');
            $table->dropColumn('city');
            $table->dropColumn('region_name');
            $table->dropColumn('barangay');
        });
    }
}
