<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message','seen', 'last_message_for']; 

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function recipient(){
        return $this->belongsTo(User::class, 'last_message_for');
    }
}
