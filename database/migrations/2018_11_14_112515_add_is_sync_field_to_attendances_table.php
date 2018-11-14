<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSyncFieldToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('isSync')->default(0);
            $table->boolean('isSignInImageSync')->default(0);
            $table->boolean('isSignOutImageSync')->default(0);
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
            $table->columnDrop('isSync');
             $table->columnDrop('isSignInImageSync');
             $table->columnDrop('isSignOutImageSync');
        });
    }
}
