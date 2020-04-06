<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'classification'
    ];

    public function surveys()
    {
        return $this->belongsToMany(Survey::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
