<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesType extends Model
{
    protected $fillable = [
       'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function expenses() {
        return $this->hasMany(Expenses::class);
    }
}
