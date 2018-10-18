<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['seen']; 

    public function user(){
        return $this->belongsTo(User::class);
    }
}
