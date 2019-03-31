<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesmanInternalOrder extends Model
{
    protected $fillable = [
        'user_id',
        'charge_type',
        'internal_order',
        'sap_server'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function chargeType()
    {
        return $this->belongsTo(ChargeType::class,'charge_type','name');
    }
}
