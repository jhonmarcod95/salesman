<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetailError extends Model
{
    use SoftDeletes;

    protected $fillable = ['payment_header_error_id', 'return_message_type', 'return_message_id','return_message_number', 'return_message_description'];
}
