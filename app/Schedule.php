<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
        'remarks'
    ];

    public static function createScheduleCode($type){
        $code = collect(DB::select('SELECT f_schedule_id(\'' . $type . '\') AS code'))->first()->code;
        return $type . '-' . $code;
    }

    public static function filter($from, $to, $uids, $codes){
        return Schedule::join('schedule_types', 'schedule_types.id', 'schedules.type')
            ->join('users', 'users.id', 'schedules.user_id')
            ->join('background_colors', 'background_colors.id', 'schedule_types.color')
            ->whereBetween('date', [$from, $to])
            ->whereIn('user_id', $uids)
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
                'users.name as full_name',
                'background_colors.color'
            ]);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function attendances(){
        return $this->hasOne(Attendance::class, 'schedule_id', 'id');
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
