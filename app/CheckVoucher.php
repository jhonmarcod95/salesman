<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckVoucher extends Model
{
    //

    public function company(){
        return $this->hasOne(Company::class, 'code', 'company_code');
    }

    public function checkInfo(){
        return $this->hasOne(CheckInfo::class);
    }


}
