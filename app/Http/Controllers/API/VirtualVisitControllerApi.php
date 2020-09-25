<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VirtualVisitImport;
use App\Schedule;
use App\ScheduleTypes;
use Carbon\Carbon;
use DB;

class VirtualVisitControllerApi extends Controller
{
    /**
     * Import Schedules Database
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx'
        ]);

        $path = $request->file('import_file');
        $import = new VirtualVisitImport;
        $collection = Excel::toCollection($import, $path);

        $scheduleDistribution = ceil(count($collection[0]) / 5);

        $filerSchedules = collect($collection[0])
            ->chunk($scheduleDistribution)
            ->map(function ($items, $key) {
                return collect($items)
                    ->map(function ($item) use ($key) {
                        return array(
                            'user_id' => $item['user_id'],
                            'type' => $item['type'],
                            'code' => $item['code'],
                            'name' => $item['name'],
                            'address' => $item['address'],
                            'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
                            'start_time' => Carbon::createFromTimeString('08:00'),
                            'end_time' => Carbon::createFromTimeString('18:00'),
                            'lat' => 0,
                            'lng' => 0,
                            'status' => 2,
                            'is_generated' => 1,
                            'km_distance' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        );
                    })->all();
            })
            ->each(function ($items, $key) {
                foreach ($items as $item) {
                    $schedule = Schedule::firstOrNew(
                        ['code' => $item["code"], 'date' => $item["date"]],
                        [
                            'user_id' => $item["user_id"],
                            'type' => 7,
                            'name' => $item["name"],
                            'address' => $item["address"],
                            'start_time' => $item["start_time"],
                            'end_time' => $item["end_time"],
                            'lat' => 0,
                            'lng' => 0,
                            'status' => 2,
                            'is_generated' => 1,
                            'km_distance' => 0,
                            'created_at' => $item["created_at"],
                            'updated_at' => $item["updated_at"]
                        ]
                    );
                    $schedule->save();
                }
                // DB::table('schedules')->insert($items);
            });

        return $filerSchedules;
    }
}
