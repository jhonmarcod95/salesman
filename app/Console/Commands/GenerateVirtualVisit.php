<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Schedule;
use Carbon\Carbon;
use DB;

class GenerateVirtualVisit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate-virtual-visit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to distribute schedules for salesman';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->saveCounter = 0;
    }

    /**
     * Function for generate schedules from the table
     */
    public function generateVirturalVisit()
    {
        // Get the schedule from table where type is Virtual Visit
        $schedules = Schedule::where('type',7)
                    ->distinct('code')
                    // ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                    ->whereBetween('created_at', [Carbon::parse('2020-09-01')->startOfMonth(), Carbon::parse('2020-09-01')->endOfMonth()])
                    // ->where('date', '<', Carbon::today()->startOfWeek()->addWeek(1))
                    ->get()
                    ->groupBy('user_id');

        $arraySchedules = [];
        foreach($schedules as $schedule) {
            array_push($arraySchedules, $schedule);
        }

        $filteredSchedules = collect($arraySchedules)
        ->map(function ($item, $key) {
            return collect($item)
                    ->map(function ($x, $k) {
                        return array(
                            'user_id' => $x->user_id,
                            'type' => $x->type,
                            'code' => $x->code,
                            'name' => $x->name,
                            'address' => $x->address,
                            'date' => $x->date,
                            'start_time' => $x->start_time,
                            'end_time' => $x->end_time,
                            'lat' => 0,
                            'lng' => 0,
                            'status' => 2,
                            'is_generated' => 1,
                            'km_distance' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        );
                    })->all();
        })->each(function ($items, $key) {

            $getChunk = ceil(count($items) / 5);


            $this->info("\n\n"."Generate Virutual Visit Schedules"."\n");
            $this->output->progressStart(count($items));

            foreach(array_chunk($items, $getChunk) as $key => $item) {

                sleep(1);

                foreach($item as $i) {

                    $schedule = Schedule::firstOrNew(
                        [
                            'code' => $i['code'],
                            'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
                        ],
                        [
                            'user_id' => $i['user_id'],
                            'type' => 7,
                            'name' => $i['name'],
                            'address' => $i['address'],
                            'start_time' => $i['start_time'],
                            'end_time' => $i['end_time'],
                            'lat' => 0,
                            'lng' => 0,
                            'status' => 2,
                            'is_generated' => 1,
                            'km_distance' => 0,
                            'created_at' => $i['created_at'],
                            'updated_at' => $i['updated_at']
                        ]);
                    $schedule->save();
                }

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
        });

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting Generate Virtual Visit');
        $this->generateVirturalVisit();
    }
}
