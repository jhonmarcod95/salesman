<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentHeaderError extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'ap_id', 'cover_week'];

    // Relationship
    public function paymentHeaderDetailError() {
        return $this->hasMany(PaymentDetailError::class);
    }
}
