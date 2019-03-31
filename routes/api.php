<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

// Route API setup for Mobile Client
Route::group(['middleware' => 'auth:api'], function() {

    // Expenses
    Route::get('expenses/types','AppAPIController@getExpensesType');
    Route::get('expenses','AppAPIController@getExpenses');
    Route::post('expenses','AppAPIController@storeExpenses');
    Route::post('expenses/{expense}','AppAPIController@updateExpense');
    Route::post('expenses/attach/{expense}','AppAPIController@uploadExpensesReciept');
    Route::delete('expenses/{expense}','AppAPIController@deleteExpense');
    Route::delete('sweep/expenses','AppAPIController@sweepExpenses');

    //Expenses Entries
    Route::get('expensesEntries','AppAPIController@expensesEntries');
    Route::post('expensesEntries','AppAPIController@storeExpensesEntries');
    Route::get('expensesEntries/show/{expensesEntries}','AppAPIController@showExpensesEntries');

    //Schedules
    Route::get('schedules','AppAPIController@getSchedules');
    Route::get('schedules/status','AppAPIController@completedToday');
    Route::get('schedules/daily','AppAPIController@dailySchedule');
    Route::get('schedules/exists','AppAPIController@checkHasSchedule');
    Route::get('schedules/visited/{schedule}','AppAPIController@markedVisited');
    Route::get('schedules/current','AppAPIController@getCurrentSchedule');

    //Attendance
    Route::post('attendances/signin','AppAPIController@signIn');
    Route::post('attendances/signout/{schedule}','AppAPIController@signOut');
    Route::post('attendances/sync/signin','AppAPIController@syncSignIn');
    Route::post('attendances/sync/signout/{schedule}','AppAPIController@syncSignOut');
    Route::post('attendances/sync','AppAPIController@syncAttendances');

    //Payments
    Route::get('payments','AppAPIController@getPayments');

    // Requests Schedules
    Route::get('customers','RequestsAPIController@customers');
    Route::get('requests','RequestsAPIController@index');
    Route::post('requests','RequestsAPIController@store');
    Route::post('requests/{requestSchedule}','RequestsAPIController@update');
    Route::delete('requests/{requestSchedule}','RequestsAPIController@destroy');

    // Receipt Expenses
    Route::get('receipt/expenses/{receiptExpense}','AppAPIController@receiptExpense');
    Route::post('receipt/expenses','AppAPIController@storeReceiptExpense');
    Route::post('receipt/expenses/{receiptExpense}','AppAPIController@updateReceiptExpense');
    Route::get('receipt/tin-numbers','AppAPIController@getTinNumbers');

    //Budget & Balance Checking API
    Route::get('internal_orders','AppAPIController@checkUserBalance');
    Route::get('check_internal_order/{expense_type}','AppAPIController@checkInternalOrder');
    Route::get('charge_type/{expense_type}','AppAPIController@checkChargeType');
    Route::get('sap/budget_check/{expense_type}','AppAPIController@checkBudget');

    // Expense Representation API
    Route::post('receipt/representation','API\ExpenseRepresentationController@store');


    //Users
    Route::get('user', 'AppAPIController@getCurrentUser');
    // Route::get('users','API\UserApiController@index');
    Route::post('users/update','API\UserApiController@update');

});
