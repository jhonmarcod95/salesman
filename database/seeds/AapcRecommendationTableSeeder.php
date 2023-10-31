<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AapcRecommendationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_recommendations')->insert([
            array(
                'name' => 'Bio480 Plus',
                'brand_type' => 'Herbicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Starshine',
                'brand_type' => 'Herbicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'El Nino',
                'brand_type' => 'Herbicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Hera',
                'brand_type' => 'Herbicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Grande',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Supra',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Vader',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Polaris',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Leafguard',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Meteor',
                'brand_type' => 'Insecticides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Yoda',
                'brand_type' => 'Fungicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Zebra Blue',
                'brand_type' => 'Fungicides',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Mantus',
                'brand_type' => 'Others',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Vaksi K',
                'brand_type' => 'Others',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Koolzet',
                'brand_type' => 'Others',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        ]);
    }
}
