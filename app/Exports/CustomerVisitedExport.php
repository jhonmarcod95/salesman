<?php

namespace App\Exports;

use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use App\Schedule;

use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerVisitedExport implements FromCollection, WithHeadings
{

    protected $data;
    /**
    * @return \Illuminate\Support\Collection
    */

    function __construct($data)
    {
        $this->company_id = $data;
    }


    public function collection()
    {
        $date_today = date('Y-m-d');

        
        $companyId = $this->company_id;

        $users = User::select('id')->where('company_id',$companyId)->get();

        $selected_user = [];
        foreach($users as $user){
            array_push($selected_user , $user['id']);
        }

        return $current_date_data = Schedule::whereIn('schedules.user_id', $selected_user)
                                            ->where('schedules.date', '=' , $date_today)
                                            ->where('schedules.type','1')
                                            ->leftJoin('attendances', function($q){
                                                $q->on('schedules.id', '=', 'attendances.schedule_id');
                                                $q->whereNotNull('sign_in');
                                            })
                                            ->leftJoin('users', function($q){
                                                $q->on('schedules.user_id', '=', 'users.id');
                                            })
                                            ->leftJoin('companies', function($q){
                                                $q->on('companies.id', '=', 'users.company_id');
                                            })
                                            ->get([
                                                'users.name as tsr',
                                                'companies.name as company',
                                                'schedules.name',
                                                'schedules.address',
                                                'schedules.date',
                                                'attendances.sign_in',
                                                'attendances.sign_out',
                                                'attendances.sign_out',
                                            ]);
        
    }

    public function headings(): array
    {
        return [
            'TSR',
            'Company',
            'Customer',
            'Address',
            'Date',
            'Sign In',
            'Sign Out'
        ];
    }

}
