<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanterHacienda extends Model
{
    protected $fillable = [
        'hacienda_location'
    ];

    public function planter()
    {
        return $this->hasOne(Planter::class,'planter_code','planter_code');
    }
}
