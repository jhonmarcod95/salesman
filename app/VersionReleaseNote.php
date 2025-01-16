<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class VersionReleaseNote extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'version_release_id',
        'type',
        'description'
    ];

    public function vesionRelease() {
        return $this->belongsTo(VersionRelease::class);
    }
}
