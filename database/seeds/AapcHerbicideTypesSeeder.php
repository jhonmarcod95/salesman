<?php

use Illuminate\Database\Seeder;

class AapcHerbicideTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_herbicide_types')->insert([
            array('name' => 'Pre-emergent'),
            array('name' => 'Post-emergent'),
            array('name' => 'Selective'),
            array('name' => 'Non-selective'),
        ]);
    }
}
