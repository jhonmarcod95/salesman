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

    // Close visit
    Route::post('/search/close-visits','CloseVisitController@searchCloseVisit');
    Route::get('schedules/close/{schedule}','AppAPIController@closeVisit');
    Route::post('schedules/request-close','AppAPIController@requestToCloseVisit');
    Route::get('/close-visits','CloseVisitController@requestToClose');
    Route::post('/confirm/close-visits/{closeVisit}','CloseVisitController@closeVisit');
    Route::get('schedules/has-activity','AppAPIController@hasActivity');

    //Attendance
    Route::post('attendances/signin','AppAPIController@signIn');
    Route::post('attendances/signout/{schedule}','AppAPIController@signOut');
    Route::post('attendances/sync/signin','AppAPIController@syncSignIn');
    Route::post('attendances/sync/signout/{schedule}','AppAPIController@syncSignOut');
    Route::post('attendances/sync','AppAPIController@syncAttendances');

    //Payments
    Route::get('payments','AppAPIController@getPayments');

    //customer
    Route::get('customer/{customer_code}','AppAPIController@getCustomer');

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
    Route::get('real_internal_orders','AppAPIController@checkUserRealBalance');
    Route::get('check_internal_order/{expense_type}','AppAPIController@checkInternalOrder');
    Route::get('charge_type/{expense_type}','AppAPIController@checkChargeType');
    Route::get('sap/budget_check/{expense_type}','AppAPIController@checkBudget');

    // Check total spent in a month
    Route::get('total-spent','AppAPIController@totalSpent');

    // check unproceed sumbmitted expenses per month
    Route::get('unprocess-expenses/{expenses_type_id}', 'AppAPIController@getUnprocessSubmittedExpense');

    // Expense Representation API
    Route::post('receipt/representation','API\ExpenseRepresentationController@store');

    // FAQ API
    // Route::resource('faqs','API\FaqsControllerAPI');
    Route::get('faqs','API\FaqsControllerAPI@index');
    Route::post('faqs','API\FaqsControllerAPI@store');
    Route::get('faqs/{faq}','API\FaqsControllerAPI@show');

    // Transportation API
    Route::get('transportations','API\RouteTransportationsController@transportations');

    //Route Transportation
    Route::post('routeTransportations','API\RouteTransportationsController@store');
    Route::post('routeTransportations/{routeTransportation}','API\RouteTransportationsController@update');

    //Expense Bypass API
    Route::post('expense-bypass','ExpenseBypassesController@store');

    //Users
    Route::get('user', 'AppAPIController@getCurrentUser');
    Route::get('users/approver', 'AppAPIController@checkUserRole');
    // Route::get('users','API\UserApiController@index');
    Route::post('users/update','API\UserApiController@update');

    //Survey API
    Route::post('surveys/company','API\SurveyControllerApi@surveyCompany');
    Route::get('surveys/questionnaires','API\SurveyControllerApi@surveyQuestionnaires');
    Route::post('surveys/montly-questions','API\SurveyControllerApi@surveyQuestionnairesSearch');
    Route::get('brands','API\SurveyControllerApi@brands');
    Route::get('surveys','API\SurveyControllerApi@index');
    Route::post('surveys','API\SurveyControllerApi@store');
    Route::get('surveys/{survey}','API\SurveyControllerApi@show');
    Route::post('surveys/attach/{survey}','API\SurveyControllerApi@uploadSurveyPhoto');

    //Planters API
    Route::get('planters','API\PlanterVisitControllerApi@index');
    Route::get('planters-area-types', 'API\PlanterVisitControllerApi@getPlanterAreaTypes');
    Route::get('planters-customers','API\PlanterVisitControllerApi@getPlanterCustomer');
    Route::get('planters/soil-types','API\PlanterVisitControllerApi@soilTypes');
    Route::get('planters/soil-conditions','API\PlanterVisitControllerApi@soilConditions');
    Route::get('planters/crop-types', 'API\PlanterVisitControllerApi@getPlanterCropTypes');
    Route::post('planters','API\PlanterVisitControllerApi@store');
    Route::post('planters/bir/{planter}','API\PlanterVisitControllerApi@uploadBirIdPhoto');
    Route::post('planters/photo/{planter}','API\PlanterVisitControllerApi@uploadPlanterPhoto');
    Route::post('planters/parcellary/{planter}','API\PlanterVisitControllerApi@uploadParcellaryPhoto');

    //Grassroots
    Route::get('grassroots/types','API\GrassrootsController@grassrootsExpenseTypes');
    Route::post('grassroots','API\GrassrootsController@store');

});
