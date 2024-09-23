<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonthlyVerificationCapture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:verification-capture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture all Verified, Rejected, and Pendings per month per user.';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
