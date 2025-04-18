<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Schedule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'code',
        'name',
        'address',
        'date',
        'start_time',
        'end_time',
        'status',
        'remarks',
        'lat',
        'lng',
        'km_distance'
    ];

    public static function createScheduleCode($type){
        $code = collect(DB::select('SELECT f_schedule_id(\'' . $type . '\') AS code'))->first()->code;
        return $type . '-' . $code;
    }

    public static function filter($from, $to, $uids, $codes){
        $company_ids = Auth::user()->companies->pluck('id'); //used to filter with same company

        return Schedule::join('schedule_types', 'schedule_types.id', 'schedules.type')
            ->join('users', 'users.id', 'schedules.user_id')
            ->join('background_colors', 'background_colors.id', 'schedule_types.color')
            ->join('company_user', 'company_user.user_id', 'users.id')
            ->whereIn('company_user.company_id', $company_ids)
            ->whereBetween('date', [$from, $to])
            ->whereIn('schedules.user_id', $uids)
            ->whereIn('code', $codes)
            ->get([
                'schedules.id',
                'schedules.user_id',
                'schedules.type',
                'schedules.code',
                'schedules.name',
                'schedules.address',
                'schedules.date',
                'schedules.start_time',
                'schedules.end_time',
                'schedules.status',
                'schedules.remarks',
                'schedules.created_at',
                'schedules.updated_at',
                'schedules.lat',
                'schedules.lng',
                'schedules.km_distance',
                'users.name as full_name',
                'background_colors.color'
            ]);
    }

    public function user() {
        return $this->belongsTo(User::class)->select('id','name','email','company_id')->withTrashed();
    }

    public function attendances(){
        return $this->hasOne(Attendance::class, 'schedule_id', 'id')->whereNotNull('sign_out')->orderBy('created_at','DESC');
    }

    public function schedule_attendances(){
        return $this->hasOne(Attendance::class, 'schedule_id', 'id')->orderBy('created_at','DESC');
    }

    public function signinwithoutout()
    {
        return $this->hasOne(Attendance::class, 'schedule_id', 'id')->whereNull('sign_out')->orderBy('created_at','DESC');
    }

    public function schedule_type(){
        return $this->belongsTo(ScheduleTypes::class, 'type', 'id');
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function closeVisit()
    {
        return $this->hasOne(CloseVisit::class);
    }

    public function scheduleBase()
    {
        return $this->belongsTo(ScheduleBase::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class,'customer_code','code');
    }

    public function customer_orders()
    {
        return $this->hasMany(CustomerOrder::class,'customer_code','code');
    }
    
    public function salesmanAttachement()
    {
        return $this->hasOne(SalesmanAttachement::class);
    }

}
