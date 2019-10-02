<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SubParticularTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('subparticulars')->insert([
            array(
                'name' => 'Courtesy',
                'particular_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Answer to Inquiries',
                'particular_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Advance Notice and Inquiries',
                'particular_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Advance Notice and Inquiries',
                'particular_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Promptness of Delivery',
                'particular_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Item Accuracy',
                'particular_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Quantity Accurancy',
                'particular_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Among Competitors',
                'particular_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Consistency',
                'particular_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Cleanliness of Delivery Truck',
                'particular_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Cleanliness of Delivery & Compliance to Protocols',
                'particular_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Handling of Delivery & Politeness of Delivery Personnel',
                'particular_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Personnel Hygiene & Sufficiency of Manpower',
                'particular_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Quality of Visit',
                'particular_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Frequency of Visit & Reachable',
                'particular_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Assistance in Testing / Sampling of Products',
                'particular_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Provide Suggestion and Updates',
                'particular_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Responds to Emergency Service Request',
                'particular_id' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Promptness of Response',
                'particular_id' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Quality of Response',
                'particular_id' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Pest Control',
                'particular_id' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Seminars',
                'particular_id' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'Recurrence of Complaint',
                'particular_id' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
         ]);
    }
}
