<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\SalesmanAttachement;

class SalesmanAttachmentApi extends Controller
{
    public function uploadAttachment(Request $request)
    {
        file_put_contents(public_path('storage/attachments/') . $request->header('File-Name'), file_get_contents('php://input'));

        $salesmanAttachment = new SalesmanAttachement;
        $salesmanAttachment->user_id =  Auth::user()->id;
        $salesmanAttachment->schedule_id = $request->header('Schedule-Id');
        $salesmanAttachment->attachment = 'attachments/' . $request->header('File-Name');
        $salesmanAttachment->save();

        return $salesmanAttachment;
    }
}
