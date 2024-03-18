<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CustomerCompany;
use App\CustomerSaleArea;
use App\Company;

class CustomerCode extends Model
{
    protected $fillable = [
        'customer_code',
        'server',
        'name',
        'street',
        'city',
        'account_group'
    ];

    public function customercompanies(){
        return $this->hasMany(CustomerCompany::class,'customer_code','customer_code');
    }

    public function customersalesarea(){
        return $this->hasMany(CustomerSaleArea::class,'customer_code','customer_code');
    }

    
}
