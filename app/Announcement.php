<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Announcement extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;

    // Relationships
    public function user(){
        return $this->belongsTo(User::class);
    }
}
