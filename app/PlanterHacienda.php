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
        return $this->belongsTo(Planter::class);
    }
}
