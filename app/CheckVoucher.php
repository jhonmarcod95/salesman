<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckVoucher extends Model
{
    use SoftDeletes;
    use \Awobaz\Compoships\Compoships;

    public function company(){
        return $this->hasOne(Company::class, 'code', 'company_code');
    }

    public function checkInfo(){
        return $this->hasOne(CheckInfo::class);
    }


}
