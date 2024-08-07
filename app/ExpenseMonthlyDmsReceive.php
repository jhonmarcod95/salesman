<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseMonthlyDmsReceive extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'dms_qr_code'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
