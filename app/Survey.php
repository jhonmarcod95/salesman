<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'remarks',
        'customer_photo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }

    public function particulars()
    {
        return $this->belongsToMany(Particular::class);
    }

    public function subparticular()
    {
        return $this->belongsToMany(Subparticular::class);
    }


}
