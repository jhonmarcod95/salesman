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
    // public function province(){
    //     return $this->hasOne('App/')
    // }
}
