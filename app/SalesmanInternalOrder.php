<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SalesmanInternalOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'charge_type',
        'internal_order',
        'sap_server',
        'uom',
        'gl_account_id'
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

    public function gl_account(){
        return $this->belongsTo(GlAccount::class);
    }
}
