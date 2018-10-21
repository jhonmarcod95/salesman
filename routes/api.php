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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

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
    Route::get('schedules/visited/{schedule}','AppAPIController@markedVisited');

});
