<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


use Maatwebsite\Excel\Excel as BaseExcel;
use App\Exports\CustomerVisitedExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerVisitedExportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->company_id = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date_today = date('Y-m-d');
        $attachment = Excel::raw(new CustomerVisitedExport($this->company_id), BaseExcel::XLSX);
        return $this->subject('Customer Last Visited')->attachData($attachment, 'CustomerVisited.xlsx')->with([
            'date_today'  =>  $date_today
        ])
        ->view('mail.customer_visited_mail');
    }
}
