<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DashboardFilter extends Model
{
    protected $fillable = [
        'user_id',
        'company',
        'tsr_id',
        'region_id',
    ];
}
