<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VirtualVisitImport;
use App\Schedule;
use App\ScheduleTypes;

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
        $data = Excel::import(new VirtualVisitImport, $path);

        return response()->json(['message' => 'uploaded successfully'], 200);
    }
}
