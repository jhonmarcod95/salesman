<?php

use Illuminate\Database\Seeder;

class AapDiseaseTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_disease_types')->insert([
            array('name' => 'Blight'),
            array('name' => 'Antracnose'),
            array('name' => 'Spots'),
        ]);
    }
}
