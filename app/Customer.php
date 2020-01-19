<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function visits(){
        return $this->hasMany('App\Schedule', 'code', 'customer_code')->where('schedules.status', '=', '1')->where('schedules.type', '=', '1');
    }

    public function schedules(){
        return $this->hasMany('App\Schedule', 'code', 'customer_code');
    }

    public function last_visited(){
        return $this->hasMany('App\Schedule', 'code', 'customer_code')
                            ->with('attendances')
                            ->where('schedules.status', '=', '1')
                            ->where('schedules.type', '=', '1')
                            ->orderBy('date', 'DESC');
    }
    
    public function classifications(){
        return $this->hasOne('App\CustomerClassification', 'id', 'classification');
    }
    public function statuses(){
        return $this->hasOne('App\CustomerClassification', 'id', 'status');
    }
    public function provinces(){
        return $this->hasOne('App\Province', 'id', 'province_id');
    }
}
