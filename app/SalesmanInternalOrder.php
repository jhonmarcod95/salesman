<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SalesmanInternalOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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

    public function balanceHistory()
    {
        return $this->hasMany(BalanceHistory::class,'internal_order','internal_order');
    }
}
