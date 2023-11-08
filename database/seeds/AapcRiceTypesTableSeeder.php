<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AapcRiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_rice_types')->insert([
            array(
                'name' => 'Direct seeded Rice',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Transplanted Rice',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
