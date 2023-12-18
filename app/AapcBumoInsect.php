<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcBumoInsect extends Model
{
    public function insectType()
    {
        return $this->belongsTo(AapcInsectType::class,'insect_type_id');
    }
}
