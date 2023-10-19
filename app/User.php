<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Passport\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasApiTokens, Notifiable, SoftDeletes, HasRoleAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    // Relationships

    public function expenses() {
        return $this->hasMany(Expense::class);
    }

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }

    public function announcements(){
        return $this->hasMany(Announcement::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }

    public function technicalSales() {
        return $this->hasMany(TechnicalSalesRepresentative::class,'user_id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function companies() {
        return $this->belongsToMany(Company::class);
    }

    public function requestSchedules() {
        return $this->hasMany(RequestSchedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function vendor(){
        return $this->hasOne(SalesmanVendor::class);
    }

    public function receiptExpenses()
    {
        return $this->hasMany(ReceiptExpense::class);
    }

    public function internalOrders()
    {
        return $this->hasMany(SalesmanInternalOrder::class);
    }

    public function location(){
        return $this->belongsToMany(Location::class);
    }

    public function expenseRate(){
        return $this->hasMany(ExpenseRate::class);
    }

    public function routeTransportations()
    {
        return $this->hasMany(RouteTransportation::class);
    }

    public function expenseBypasses()
    {
        return $this->hasMany(ExpenseBypass::class);
    }

    public function closeVisits()
    {
        return $this->hasMany(CloseVisit::class);
    }

    public function farmerMeetings()
    {
        return $this->hasMany(FarmerMeeting::class);
    }
}
