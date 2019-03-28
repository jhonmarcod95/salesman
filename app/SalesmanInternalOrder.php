<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesmanInternalOrder extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargeType()
    {
        return $this->belongsTo(ChargeType::class,'charge_type','name');
    }
}
