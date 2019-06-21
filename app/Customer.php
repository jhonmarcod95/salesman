<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;
    
    protected $fillable = ['company_id', 'area', 'classification', 'customer_code', 'name', 'street', 'town_city',
    'province_id', 'google_address', 'lat', 'lng' ,'telephone_1', 'telephone_2', 'fax_number', 'remarks'];

    protected $dates = ['deleted_at'];
    // public function province(){
    //     return $this->hasOne('App/')
    // }
}
