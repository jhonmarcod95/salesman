<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AapcRegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aapc_regions')->insert([
            array('name' => "ILOCOS"),
            array('name' => "CAGAYAN VALLEY"),
            array('name' => "CENTRAL LUZON"),
            array('name' => "CALABARZON"),
            array('name' => "MIMAROPA"),
            array('name' => "BICOL"),
            array('name' => "WESTERN VISAYAS"),
            array('name' => "CENTRAL VISAYAS"),
            array('name' => "EASTERN VISAYAS"),
            array('name' => "WESTERN MINDANAO"),
            array('name' => "NORTHERN MINDANAO"),
            array('name' => "CENTRAL MINDANAO"),
            array('name' => "SOUTHERN MINDANAO"),
            array('name' => "SOCCSKSARGEN"),
            array('name' => "CARAGA"),
            array('name' => "ARMM"),
            array('name' => "CORDILLERA ADM REG'N"),
            array('name' => "NAT'L CAPITAL REGION"),
            array('name' => "Ilocos"),
            array('name' => "Bicol"),
            array('name' => "Western Visayas"),
            array('name' => "Zamboanga Peninsula"),
            array('name' => "Davao"),
            array('name' => "SOCCSKSARGEN"),
            array('name' => "CARAGA"),
            array('name' => "ARMM ( Autonomous Re"),
            array('name' => "CAR (Cordillera Admi"),
            array('name' => "NCR"),
            array('name' => "CALABARZON"),
            array('name' => "MIMAROPA"),
            array('name' => "Ilocos Region"),
            array('name' => "CALABARZON"),
            array('name' => "Bicol Region"),
            array('name' => "Western Visayas"),
            array('name' => "Zamboanga Peninsula"),
            array('name' => "Davao Region"),
            array('name' => "SOCCSKSARGEN"),
            array('name' => "NCR Nat. Capital Reg"),
            array('name' => "CAR Cordillera AR"),
            array('name' => "ARMM Muslim Mindanao"),
            array('name' => "Caraga Region"),
            array('name' => "MIMAROPA Region"),
            array('name' => "Ilocos"),
            array('name' => "South Luzon"),
            array('name' => "MIMAROPA"),
            array('name' => "Bicol"),
            array('name' => "West Visayas"),
            array('name' => "Western Mindanao"),
            array('name' => "Central Mindanao"),
            array('name' => "South Mindanao"),
            array('name' => "CARAGA"),
            array('name' => "ARMM"),
            array('name' => "Cordillera Adm Regn"),
            array('name' => "Nat'l Capital Region"),
        ]);
    }
}
