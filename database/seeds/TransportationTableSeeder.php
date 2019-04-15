<?php

use Illuminate\Database\Seeder;
use App\Transportation;
use Carbon\Carbon;

class TransportationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Transportation::where('mode', '=', 'Jeepney')->first() === null) {
            Transportation::create([
                'mode' => 'Jeepney',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        if (Transportation::where('mode', '=', 'Tricyle')->first() === null) {
            Transportation::create([
                'mode' => 'Tricyle',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        if (Transportation::where('mode', '=', 'Habal-habal')->first() === null) {
            Transportation::create([
                'mode' => 'Habal-habal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        if (Transportation::where('mode', '=', 'UV Express')->first() === null) {
            Transportation::create([
                'mode' => 'UV Express',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
