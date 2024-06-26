<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFbLinkFieldToAapcFarmersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aapc_farmers', function (Blueprint $table) {
            $table->string('fb_link')->nullable();
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
            $table->dropColumn('fb_link');
        });
    }
}
