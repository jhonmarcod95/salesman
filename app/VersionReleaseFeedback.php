<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class VersionReleaseFeedback extends Model implements Auditable
{
    protected $connection = 'mysql';
    protected $table = 'version_release_feedbacks';

    use SoftDeletes,\OwenIt\Auditing\Auditable;

    protected $fillable = [
        'version_release_id','user_id','feedback'
    ];

    public function versionRelease() {
        return $this->belongsTo(VersionRelease::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}