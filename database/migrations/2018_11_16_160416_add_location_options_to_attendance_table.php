<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationOptionsToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('sign_in_latitude')->nullable();
            $table->string('sign_in_longitude')->nullable();
            $table->string('sign_in_speed')->nullable();
            $table->string('sign_out_latitude')->nullable();
            $table->string('sign_out_longitude')->nullable();
            $table->string('sign_out_speed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('sign_in_latitude');
            $table->dropColumn('sign_in_longitude');
            $table->dropColumn('sign_in_speed');
            $table->dropColumn('sign_out_latitude');
            $table->dropColumn('sign_out_longitude');
            $table->dropColumn('sign_out_speed');
        });
    }
}
