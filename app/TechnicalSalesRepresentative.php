<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TechnicalSalesRepresentative extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id', 'employee_id', 'company_id', 'last_name', 'first_name','middle_name',
    'middle_initial', 'suffix', 'email', 'address', 'contact_number', 'date_of_birth', 'date_hired', 'contact_person', 'personal_email', 'plate_number'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

}
