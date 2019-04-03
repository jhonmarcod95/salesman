<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $model = [
        'question',
        'answer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
