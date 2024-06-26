<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RequestSchedule extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'type',
        'code',
        'name',
        'address',
        'date',
        'start_time',
        'end_time',
        'status',
        'remarks'
    ];

    // Belongs to user
    public function user() {
        return $this->belongsTo(User::class);
    }

}
