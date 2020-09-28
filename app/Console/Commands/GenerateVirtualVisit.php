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
                    ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
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
                    ->chunk(5)
                    ->map(function ($x, $k) use ($key) {
                        return array(
                            'user_id' => $x->user_id,
                            'type' => $x->type,
                            'code' => $x->code,
                            'name' => $x->name,
                            'address' => $x->address,
                            'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
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
        });

        echo json_encode($filteredSchedules, JSON_PRETTY_PRINT);

        
            
        // return collect($schedules->get())->groupBy('user_id')->toArray();
        // dd(collect($schedules->get())->groupBy('user_id')->toArray());

        // get the chunk

        // $scheduleDistribution = ceil($schedules->count() / 5) < 5 ? 5 : ceil($schedules->count());
        // $scheduleDistribution = ceil($schedules->count() / 5);

        // Return the schedule to be insert into schedule table
        // $filerSchedules = collect($schedules->get())
        //     ->chunk($scheduleDistribution)
        //     ->map(function ($items, $key) {
        //         return collect($items)
        //             ->map(function ($item) use ($key) {
        //                 return array(
        //                     'user_id' => $item->user_id,
        //                     'type' => $item->type,
        //                     'code' => $item->code,
        //                     'name' => $item->name,
        //                     'address' => $item->address,
        //                     'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
        //                     'start_time' => $item->start_time,
        //                     'end_time' => $item->end_time,
        //                     'lat' => 0,
        //                     'lng' => 0,
        //                     'status' => 2,
        //                     'is_generated' => 1,
        //                     'km_distance' => 0,
        //                     'created_at' => Carbon::now(),
        //                     'updated_at' => Carbon::now()
        //                 );
        //             })->all();
        //     })
        //     ->each(function ($items, $key)   {

        //         $this->info("\n\n"."Inserting for date : ". Carbon::today()->startOfWeek()->addWeek(1)->addDays($key)."\n");
        //         $this->output->progressStart(count($items));

        //         foreach($items as $item) {

        //             sleep(1);

        //             $schedule = Schedule::firstOrNew(
        //                 ['code' => $item["code"],'date' => $item["date"]],
        //                 [
        //                     'user_id' => $item["user_id"],
        //                     'type' => 7,
        //                     'name' => $item["name"],
        //                     'address' => $item["address"],
        //                     'start_time' => $item["start_time"],
        //                     'end_time' => $item["end_time"],
        //                     'lat' => 0,
        //                     'lng' => 0,
        //                     'status' => 2,
        //                     'is_generated' => 1,
        //                     'km_distance' => 0,
        //                     'created_at' => $item["created_at"],
        //                     'updated_at' => $item["updated_at"]
        //                 ]);

        //             $schedule->save();

        //             $this->output->progressAdvance();
        //         }

        //         $this->output->progressFinish();

        //         $this->info( "Load Finished");
        //         // DB::table('schedules')->insert($items);
        //     });


        // $filerSchedules = collect($schedules->get())
        //     ->groupBy(function ($item, $key) {
        //         return $item['user_id']->;
        //     })
        //     ->each(function ($items, $key) {
        //         return count($items);
        //     });

            // ->chunk($scheduleDistribution)
            // ->map(function ($items, $key) {
            //     return collect($items)
            //         ->groupBy(function ($item, $key) {
            //               $filteredUsers = $item['user_id'];
            //               return $filteredUsers->values()->all();
            //         }); 


                    // ->map(function ($item) use ($key) {
                    //     return array(
                    //         'user_id' => $item->user_id,
                    //         'type' => $item->type,
                    //         'code' => $item->code,
                    //         'name' => $item->name,
                    //         'address' => $item->address,
                    //         'date' => Carbon::today()->startOfWeek()->addWeek(1)->addDays($key),
                    //         'start_time' => $item->start_time,
                    //         'end_time' => $item->end_time,
                    //         'lat' => 0,
                    //         'lng' => 0,
                    //         'status' => 2,
                    //         'is_generated' => 1,
                    //         'km_distance' => 0,
                    //         'created_at' => Carbon::now(),
                    //         'updated_at' => Carbon::now()
                    //     );
                    // })->all();
            // });
            // ->each(function ($items, $key)   {

            //     $this->info("\n\n"."Inserting for date : ". Carbon::today()->startOfWeek()->addWeek(1)->addDays($key)."\n");
            //     $this->output->progressStart(count($items));

            //     foreach($items as $item) {

            //         sleep(1);

            //         $schedule = Schedule::firstOrNew(
            //             ['code' => $item["code"],'date' => $item["date"]],
            //             [
            //                 'user_id' => $item["user_id"],
            //                 'type' => 7,
            //                 'name' => $item["name"],
            //                 'address' => $item["address"],
            //                 'start_time' => $item["start_time"],
            //                 'end_time' => $item["end_time"],
            //                 'lat' => 0,
            //                 'lng' => 0,
            //                 'status' => 2,
            //                 'is_generated' => 1,
            //                 'km_distance' => 0,
            //                 'created_at' => $item["created_at"],
            //                 'updated_at' => $item["updated_at"]
            //             ]);

            //         $schedule->save();

            //         $this->output->progressAdvance();
            //     }

            //     $this->output->progressFinish();

            //     $this->info( "Load Finished");
            //     // DB::table('schedules')->insert($items);
            // });


        // dd(json_par$filerSchedules);

        // echo json_encode($schedules, JSON_PRETTY_PRINT);
          
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
