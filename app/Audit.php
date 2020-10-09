<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    public function schedule(){
        return $this->hasOne(Schedule::class, 'id','auditable_id')->select('id','user_id','code','name','address','date');
    }
}
