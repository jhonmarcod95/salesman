<?php

namespace App\Http\Controllers;

use Auth;
use Carbon;
use App\Message;
use App\Attendance;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session(['header_text' => 'Attendance Report']);
        $message = Message::where('user_id', '!=', Auth::user()->id)->get();
        $notification = 0;
        foreach($message as $notif){

            $ids = collect(json_decode($notif->seen, true))->pluck('id');
            if(!$ids->contains(Auth::user()->id)){
                $notification++;
            }
        }

        return view('attendance-report.index',compact('notification'));
    }


    /**
     * Get all Attendance Report
     *
     * @return \Illuminate\Http\Response
     */

    public function indexData(){
        return User::with('schedules', 'attendances')
                    ->whereHas('roles', function($q) {
                        $q->where('role_id', 3);
                    })->get();
    }

    /**
     * Get all Schedule by date
     *
     * @return \Illuminate\Http\Response
     */

    public function generateBydate(Request $request){
        ini_set('memory_limit', '2048M');

        $selectedSchedulType = $request->schedule_type;
        $keyword = $request->keywords;

        if($request->company){
            $company = $request->company;
        }else{
            $company = Auth::user()->companies->first()->id;
        }
        
        $regions = [];
        if($request->selectedRegion){
            foreach($request->selectedRegion as $region_data){
                array_push($regions, $region_data['id']);
            }
        }

        if ($keyword == '' || $keyword == null) {
            if ($request->startDate == date('Y-m-d') && $request->endDate == date('Y-m-d') && ($request->company == null && $regions == null)) {
                $schedule = Schedule::with('user','user.roles','customer.provinces.regions','attendances','signinwithoutout','schedule_type', 'salesmanAttachement')
                    ->whereHas('user' , function($q){
                        $q->when(Auth::user()->level() < 6, function($query){
                            $query->wherehas('roles', function ($q){
                                $q->where('level','<',6);
                            });
                        });
                        $q->whereHas('companies', function ($q){
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        });
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->paginate(10);
            }
            else{
                if(Auth::user()->level() < 8 && !Auth::user()->hasRole(['hr', 'audit'])){
                    $schedule = Schedule::with('user','user.roles', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                    ->whereHas('user' , function($q){
                        $q->when(Auth::user()->level() < 6, function($query){
                            $query->wherehas('roles', function ($q){
                                $q->where('level','<',6);
                            });
                        });
                        $q->whereHas('companies', function ($q){
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        });
                    })
                    ->when(!empty($regions), function($q) use ($regions) {
                        $q->whereHas('customer' , function($q) use ($regions) {
                            $q->whereHas('provinces', function ($q) use ($regions){
                                $q->whereIn('region_id', $regions);
                            });
                        });
                    })
                    ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                        return $query->where('type', $selectedSchedulType);
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->paginate(10);
                }else{
                    
                    $schedule = Schedule::with('user','user.roles', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                    ->when($company, function ($query) use ($company) {
                        $query->whereHas('user', function($q) use ($company){
                            $q->when(Auth::user()->level() < 6, function($query){
                                $query->wherehas('roles', function ($q){
                                    $q->where('level','<',6);
                                });
                            });
                            $q->whereHas('companies', function ($q) use ($company){
                                $q->where('company_id', $company);
                            });
                        });
                    })
                    ->when(!empty($regions), function($q) use ($regions) {
                        $q->whereHas('customer' , function($q) use ($regions) {
                            $q->whereHas('provinces', function ($q) use ($regions){
                                $q->whereIn('region_id', $regions);
                            });
                        });
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->paginate(10);
                }
            }
        } else {
            if(Auth::user()->level() < 8 && !Auth::user()->hasRole(['hr', 'audit'])){
                $schedule = Schedule::with('user','user.roles', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->whereHas('user' , function($q){
                    $q->when(Auth::user()->level() < 6, function($query){
                        $query->wherehas('roles', function ($q){
                            $q->where('level','<',6);
                        });
                    });
                    $q->whereHas('companies', function ($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->orderBy('date', 'desc')
                ->paginate(10);
            } else {
                $schedule = Schedule::with('user','user.roles', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->when($company, function ($query) use ($company) {
                    $query->whereHas('user', function($q) use ($company){
                        $q->when(Auth::user()->level() < 6, function($query){
                            $query->wherehas('roles', function ($q){
                                $q->where('level','<',6);
                            });
                        });
                        $q->whereHas('companies', function ($q) use ($company){
                            $q->where('company_id', $company);
                        });
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                ->orderBy('date', 'desc')
                ->paginate(10);
            }
        }

        return $schedule;

        /* $new_schedule = [];
        if(!Auth::user()->hasRole('hr')){
            // Executive and VP Roles
            if(Auth::user()->level() >= 6){
                $new_schedule = $schedule;
            }else{
                foreach($schedule->pluck('user') as $key => $value){
                    // AVP and Coordinator  roles
                    if(Auth::user()->level() < 6){
                        if($value->roles[0]['level'] < 6){
                            $new_schedule[] = $schedule[$key];
                        }
                    }else{
                        $new_schedule = [];
                    }
                }
            }
        }else {  $new_schedule = $schedule; } */
        
        // return $new_schedule;

        /* $schedule = Schedule::with('user','customer.provinces.regions','attendances','signinwithoutout','schedule_type', 'salesmanAttachement')
            ->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })
            ->whereBetween('date',[$request->startDate,$request->endDate])
            ->orderBy('date', 'desc')
            ->get();

        $new_schedule = [];

        if(!Auth::user()->hasRole('hr')){
            // Executive and VP Roles
            if(Auth::user()->level() >= 6){
                $new_schedule = $schedule;
            }else{
                foreach($schedule->pluck('user') as $key => $value){
                    // AVP and Coordinator  roles
                    if(Auth::user()->level() < 6){
                        if($value->roles[0]['level'] < 6){
                            $new_schedule[] = $schedule[$key];
                        }
                    }else{
                        $new_schedule = [];
                    }
                }
            }
        }else {
            $new_schedule = $schedule;
        }

        return $new_schedule; */
    }

    public function generateByToday(Request $request){
        if (($request->startDate == '' || $request->startDate == null) || ($request->endDate == '' || $request->endDate == null)) {
            $request->startDate = date('Y-m-d');
            $request->endDate = date('Y-m-d');
        }

        $selectedSchedulType = $request->schedule_type;

        if($request->company){
            $company = $request->company;
        }else{
            $company = Auth::user()->companies->first()->id;
        }

        $regions = [];
        if($request->selectedRegion){
            foreach($request->selectedRegion as $region_data){
                array_push($regions, $region_data['id']);
            }
        }

        if ($request->startDate == date('Y-m-d') && $request->endDate == date('Y-m-d')) {
            $schedule = Schedule::with('user','customer.provinces.regions','attendances','signinwithoutout','schedule_type', 'salesmanAttachement')
                ->whereHas('user' , function($q){
                    $q->whereHas('companies', function ($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
        }
        else{
            if(Auth::user()->level() < 8 && !Auth::user()->hasRole(['hr', 'audit'])){
                $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->whereHas('user' , function($q){
                    $q->whereHas('companies', function ($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
            }else{
                $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->when($company, function ($query) use ($company) {
                    $query->whereHas('user', function($q) use ($company){
                        $q->whereHas('companies', function ($q) use ($company){
                            $q->where('company_id', $company);
                        });
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                /* ->whereDate('date', '>=',  $request->startDate)
                ->whereDate('date' ,'<=', $request->endDate) */
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->orderBy('date', 'desc')
                ->paginate(10);
            }
        }

        $new_schedule = [];
        if(!Auth::user()->hasRole('hr')){
            // Executive and VP Roles
            if(Auth::user()->level() >= 6){
                $new_schedule = $schedule;
            }else{
                foreach($schedule->pluck('user') as $key => $value){
                    // AVP and Coordinator  roles
                    if(Auth::user()->level() < 6){
                        if($value->roles[0]['level'] < 6){
                            $new_schedule[] = $schedule[$key];
                        }
                    }else{
                        $new_schedule = [];
                    }
                }
            }
        }else {  $new_schedule = $schedule; }
        return $new_schedule;
    }

    //ORIGINAL FUNCTION
    /* public function generateByToday(Request $request){
        if (($request->startDate == '' || $request->startDate == null) || ($request->endDate == '' || $request->endDate == null)) {
            $request->startDate = date('Y-m-d');
            $request->endDate = date('Y-m-d');
        }

        $schedule = Schedule::with('user','customer.provinces.regions','attendances','signinwithoutout','schedule_type', 'salesmanAttachement')
            ->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })
            ->whereBetween('date',[$request->startDate,$request->endDate])
            ->orderBy('date', 'desc')
            ->get();

        $new_schedule = [];

        if(!Auth::user()->hasRole('hr')){
            // Executive and VP Roles
            if(Auth::user()->level() >= 6){
                $new_schedule = $schedule;
            }else{
                foreach($schedule->pluck('user') as $key => $value){
                    // AVP and Coordinator  roles
                    if(Auth::user()->level() < 6){
                        if($value->roles[0]['level'] < 6){
                            $new_schedule[] = $schedule[$key];
                        }
                    }else{
                        $new_schedule = [];
                    }
                }
            }
        }else {
            $new_schedule = $schedule;
        }

        return $new_schedule;
    } */

    public function exportData(Request $request){
        $request->validate([
            'startDate' => 'required',
            'endDate' => 'after_or_equal:start_date'
        ]);
        
        $keyword = $request->keywords;
        $selectedSchedulType = $request->schedule_type;

        if($request->company){
            $company = $request->company;
        }else{
            $company = Auth::user()->companies->first()->id;
        }

        $regions = [];
        if($request->selectedRegion){
            foreach($request->selectedRegion as $region_data){
                array_push($regions, $region_data['id']);
            }
        }

        if ($keyword == '' || $keyword == null) {
            if ($request->startDate == date('Y-m-d') && $request->endDate == date('Y-m-d') && ($request->company == null && $regions == null)) {
                $schedule = Schedule::with('user','customer.provinces.regions','attendances','signinwithoutout','schedule_type', 'salesmanAttachement')
                    ->whereHas('user' , function($q){
                        $q->whereHas('companies', function ($q){
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        });
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->get();
            }
            else{
                if(Auth::user()->level() < 8 && !Auth::user()->hasRole(['hr', 'audit'])){
                    $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                    ->whereHas('user' , function($q){
                        $q->whereHas('companies', function ($q){
                            $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                        });
                    })
                    ->when(!empty($regions), function($q) use ($regions) {
                        $q->whereHas('customer' , function($q) use ($regions) {
                            $q->whereHas('provinces', function ($q) use ($regions){
                                $q->whereIn('region_id', $regions);
                            });
                        });
                    })
                    ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                        return $query->where('type', $selectedSchedulType);
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->get();
                }else{
                    $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                    ->when($company, function ($query) use ($company) {
                        $query->whereHas('user', function($q) use ($company){
                            $q->whereHas('companies', function ($q) use ($company){
                                $q->where('company_id', $company);
                            });
                        });
                    })
                    ->when(!empty($regions), function($q) use ($regions) {
                        $q->whereHas('customer' , function($q) use ($regions) {
                            $q->whereHas('provinces', function ($q) use ($regions){
                                $q->whereIn('region_id', $regions);
                            });
                        });
                    })
                    ->when(!empty($selectedSchedulType), function ($query, $selectedSchedulType) {
                        return $query->where('type', $selectedSchedulType);
                    })
                    ->whereBetween('date',[$request->startDate,$request->endDate])
                    ->orderBy('date', 'desc')
                    ->get();
                }
            }
        } else {
            if(Auth::user()->level() < 8 && !Auth::user()->hasRole(['hr', 'audit'])){
                $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->whereHas('user' , function($q){
                    $q->whereHas('companies', function ($q){
                        $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->orderBy('date', 'desc')
                ->get();
            } else {
                $schedule = Schedule::with('user', 'customer.provinces.regions', 'attendances','signinwithoutout','schedule_type','salesmanAttachement')
                ->when($company, function ($query) use ($company) {
                    $query->whereHas('user', function($q) use ($company){
                        $q->whereHas('companies', function ($q) use ($company){
                            $q->where('company_id', $company);
                        });
                    });
                })
                ->when(!empty($regions), function($q) use ($regions) {
                    $q->whereHas('customer' , function($q) use ($regions) {
                        $q->whereHas('provinces', function ($q) use ($regions){
                            $q->whereIn('region_id', $regions);
                        });
                    });
                })
                ->whereHas('user', function($q) use ($keyword){
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->whereBetween('date',[$request->startDate,$request->endDate])
                ->when($selectedSchedulType, function ($query, $selectedSchedulType) {
                    return $query->where('type', $selectedSchedulType);
                })
                ->orderBy('date', 'desc')
                ->get();
            }
        }

        $new_schedule = [];
        if(!Auth::user()->hasRole('hr')){
            // Executive and VP Roles
            if(Auth::user()->level() >= 6){
                $new_schedule = $schedule;
            }else{
                foreach($schedule->pluck('user') as $key => $value){
                    // AVP and Coordinator  roles
                    if(Auth::user()->level() < 6){
                        if($value->roles[0]['level'] < 6){
                            $new_schedule[] = $schedule[$key];
                        }
                    }else{
                        $new_schedule = [];
                    }
                }
            }
        }else {  $new_schedule = $schedule; }
        return $new_schedule;
    }

     /**
     * Get all area that is current visiting
     *
     * @return \Illuminate\Http\Response
     */
    public function visiting(){
        if(Auth::user()->level() < 8){

            return Attendance::with('user', 'schedule')->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            })->whereHas('schedule', function ($q){
                $q->whereNotIn('type', [4,5]);
            })->where('sign_in', Carbon\Carbon::now()->toDateString())
            ->where('sign_out', null)->orderBy('id','desc')->get();

        }

        return Attendance::with('user', 'schedule')
        ->whereHas('schedule', function ($q){
            $q->whereNotIn('type', [4,5]);
        })->where('sign_in', Carbon\Carbon::now()->toDateString())
        ->where('sign_out', null)->orderBy('id','desc')->get();

    }

    /**
     * Get all area that is visited
     *
     * @return \Illuminate\Http\Response
     */
    public function completed(){
        if(Auth::user()->level() < 8){

            return Attendance::with('user', 'schedule')->whereHas('user' , function($q){
                $q->whereHas('companies', function ($q){
                    $q->whereIn('company_id', Auth::user()->companies->pluck('id'));
                });
            }) ->whereHas('schedule', function ($q){
                $q->whereNotIn('type', [4,5]);
            })
            ->where('sign_in', Carbon\Carbon::now()->toDateString())
            ->where('sign_out','!=',null)->orderBy('id','desc')->get();

        }

        return Attendance::with('user', 'schedule')->whereDate('sign_in', Carbon\Carbon::now()->toDateString())
            ->whereHas('schedule', function ($q){
                $q->whereNotIn('type', [4,5]);
            })->whereNotNull('sign_out')->orderBy('id','desc')->get();
    }

}
