<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForceCloseDateToAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('sign_in_by')->nullable();
            $table->datetime('force_close_sign_in_date')->nullable();
            $table->datetime('force_close_sign_out_time')->nullable();
            $table->integer('sign_out_by')->nullable();
            $table->longText('sign_in_remarks')->nullable();
            $table->longText('sign_out_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
