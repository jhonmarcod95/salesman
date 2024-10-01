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

    public function validatedExpenses() {
        return $this->hasMany(Expense::class, 'verified_by')->whereIn('verified_status_id', [1,3]);
    }

    public function verifiedExpenses(){
        return $this->hasMany(Expense::class, 'verified_by')->where('verified_status_id', 1);
    }

    public function rejectedExpenses(){
        return $this->hasMany(Expense::class, 'verified_by')->where('verified_status_id', 3);
    }

    public function expensesEntries() {
        return $this->hasMany(ExpensesEntry::class);
    }

    public function expensesModel() {
        return $this->hasManyThrough(Expense::class, ExpensesEntry::class);
    }

    public function verifiedExpense() {
        return $this->hasManyThrough(Expense::class, ExpensesEntry::class)->where('verified_status_id', 1);
    }

    public function unverifiedExpense() {
        return $this->hasManyThrough(Expense::class, ExpensesEntry::class)->where('verified_status_id', 2);
    }

    public function rejectedExpense() {
        return $this->hasManyThrough(Expense::class, ExpensesEntry::class)->where('verified_status_id', 3);
    }

    public function pendingExpense() {
        return $this->hasManyThrough(Expense::class, ExpensesEntry::class)->where('verified_status_id', 0);
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

    public function expenseVerifierCoordinator() {
        return $this->hasMany(Expense::class, 'verified_by');
    }

    public function scopeUserWithExpense($query) {
        $query
        ->where('is_sales', 1)
        ->where('is_act_user', 0)
        ->whereHas('roles', function($q) {
            $q->where('with_expense', 1);
        });
    }

    public function scopeCoordinatorValidatedExpense($validatedExpenseQuery, $start_date, $end_date,$company_id, $coordinator_id = null) {
        return $validatedExpenseQuery
        ->whereHas("roles", function ($q) {
            $q->whereIn("slug", ["coordinator", "coordinator-2"]);
        })
        ->when(isset($company_id), function($query) use($company_id){
            $query->whereHas('companies', function ($verifierCompanyQuery) use ($company_id) {
                $verifierCompanyQuery->where('company_id', $company_id);
            });
        })
        ->with(['validatedExpenses' => function($query) use ($coordinator_id, $start_date, $end_date) {
            $query->when(isset($coordinator_id), function ($coordinatorQuery) use ($coordinator_id) {
                $coordinatorQuery->where('verified_by', $coordinator_id);
            })
            ->whereBetween('date_verified', [$start_date, $end_date]);
        }])
        ->withCount(['validatedExpenses' => function ($query) use ($coordinator_id, $start_date, $end_date) {
            $query->when(isset($coordinator_id), function ($coordinatorQuery) use ($coordinator_id) {
                $coordinatorQuery->where('verified_by', $coordinator_id);
            })
            ->whereBetween('date_verified', [$start_date, $end_date]);
        }])
        ->withCount(['rejectedExpenses' => function ($query) use ($coordinator_id, $start_date, $end_date) {
            $query->when(isset($coordinator_id), function ($coordinatorQuery) use ($coordinator_id) {
                $coordinatorQuery->where('verified_by', $coordinator_id);
            })
                ->whereBetween('date_verified', [$start_date, $end_date]);
        }])
        ->withCount(['verifiedExpenses' => function ($query) use ($coordinator_id, $start_date, $end_date) {
            $query->when(isset($coordinator_id), function ($coordinatorQuery) use ($coordinator_id) {
                $coordinatorQuery->where('verified_by', $coordinator_id);
            })
                ->whereBetween('date_verified', [$start_date, $end_date]);
        }]);
    }
}
