<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Schedule;
use Carbon\Carbon;

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
    }

    /**
     * Function for generate schedules from the table
     */
    public function generateVirturalVisit()
    {
        $schedules = Schedule::where('type',7)
                    ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                    ->count();

        return $schedules;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting Generate Virtual Visit');
        dd($this->generateVirturalVisit());
    }
}
