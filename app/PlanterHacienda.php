<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanterHacienda extends Model
{
    protected $fillable = [
        'planter_code',
        'name',
        'mobile_number',
        'hacienda_code',
        'planter_audit_no',
        'address',
        'area',
        'created_at',
        'updated_at'
    ];

    public function planter()
    {
        return $this->hasOne(Planter::class,'planter_code','planter_code');
    }
}
