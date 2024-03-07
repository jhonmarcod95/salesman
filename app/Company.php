<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;


    public function businessArea(){
        return $this->hasMany(BusinessArea::class);
    }

    public function glTaxcode(){
        return $this->hasMany(GlTaxcode::class, 'company_code', 'code');
    }

    public function sapServers() {
        return $this->belongsToMany(SapServer::class);
    }

    public function bankGls(){
        return $this->hasMany(BankCompanyGl::class);
    }

    public function bankChecks(){
        return $this->hasMany(BankCheckSeries::class);
    }

}
