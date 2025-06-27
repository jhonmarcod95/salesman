<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class RunCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('run-command.index');
    }
    
    //php artisan payment:autoposting {HANA/LFUG}
    public function runAutoPosting($server)
    {
        Artisan::call('payment:autoposting', ['--server' => $server]);
        return Artisan::output();
    }

    //php artisan payment:autopostingreprocessing {HANA/LFUG}
    public function runAutoPostingReProcessing($server)
    {
        Artisan::call('payment:autopostingreprocessing', ['--server' => $server]);
        return Artisan::output();
    }

    //php artisan payment:autocv
    public function runAutoCV()
    {
        Artisan::call('payment:autocv');
        return Artisan::output();
    }

    //php artisan payment:autocheck
    public function runAutoCheck()
    {
        Artisan::call('payment:autocheck');
        return Artisan::output();
    }
}
