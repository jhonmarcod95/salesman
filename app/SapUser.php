<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SapUser extends Model
{
    protected $fillable = [
        'user_id',
        'sap_id',
        'sap_password',
        'sap_server',
    ];
}
