<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TechnicalSalesRepresentative extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

}
