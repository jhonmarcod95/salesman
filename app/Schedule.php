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
