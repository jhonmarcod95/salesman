<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/manualInsert/{tsrId}', 'TsrController@manuallyInsertUser');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('logout', function(){
    return redirect('/');
  });

// Authenticated Routes
Route::group(['middleware' => 'auth'], function(){
    // Provinces
    Route::get('/provinces', 'ProvinceController@index');

    // Regions
    Route::get('/regions', 'RegionController@index');

    //Roles
    Route::get('/roles', 'RoleController@index');

    // User
    Route::post('/change-password', 'UserController@changePassword');
    // Show change password page
    Route::get('/user/change-password', 'UserController@changePasswordIndex')->name('change_password');
    // Fetch all companies
    Route::get('/companies-all', 'CompanyController@indexData');
});

// Admin Routes
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|approver|tax']], function () {
    //Schedules
    Route::get('/schedules', 'ScheduleController@index');
    Route::get('/schedules/{date_from}/{date_to}', 'ScheduleController@indexData');

    Route::get('/schedule-customer/{classification}', 'ScheduleController@scheduleCustomerData');

    Route::post('/schedules/store', 'ScheduleController@store');
    Route::patch('/schedules/update/{id}', 'ScheduleController@update');
    Route::patch('/schedules/change/{id}', 'ScheduleController@change');
    Route::delete('/schedules/destroy/{id}', 'ScheduleController@destroy');

    //Announcements
    Route::get('/announcements', 'AnnouncementController@index')->name('announcements_list');
    // fetch all announcements
    Route::get('/announcements-all', 'AnnouncementController@indexData');
    //add new Announcement
    Route::post('/announcement', 'AnnouncementController@store');
    // update Announcements
    Route::patch('/announcement/{announcement}', 'AnnouncementController@update');
    // delete Announcements
    Route::delete('/announcement/{announcement}', 'AnnouncementController@destroy');


    //User
    // show user page
    Route::get('/users', 'UserController@index')->name('users_list');
    // fetch all user
    Route::get('/users-all', 'UserController@indexData');
    //show add user page
    Route::get('/users/create', 'UserController@create');
    //add new user
    Route::post('/user', 'UserController@store');
    // show user edit page
    Route::get('/user-edit/{id}','UserController@edit');
    // show details of specific user
    Route::get('/user/show/{id}', 'UserController@show');
    // update customer
    Route::patch('/user/{user}', 'UserController@update');
    //delete user
    Route::delete('/user/{id}', 'UserController@destroy');


    Route::get('/users/show/{id}', 'UserController@show');
    Route::patch('/users/update/{id}', 'UserController@update');


    //Tsr
    // show tsr page
    Route::get('/tsr', 'TsrController@index')->name('tsr_list');
    // fetch all tsr
    Route::get('/tsr-all', 'TsrController@indexData');
    //show add tsr page
    Route::get('/tsr/create', 'TsrController@create');
    //add new customer
    Route::post('/tsr', 'TsrController@store');
    // show customer edit page
    Route::get('/tsr-edit/{id}','TsrController@edit');
    // show details of specific customer
    Route::get('/tsr/show/{id}', 'TsrController@show');
    // update customer
    Route::patch('/tsr/{technicalSalesRepresentative}', 'TsrController@update');

    //Customer
    // show customer page
    Route::get('/customers', 'CustomerController@index')->name('customers_list');
    // fetch all customer
    Route::get('/customers-all', 'CustomerController@indexData');
    //show add customer page
    Route::get('/customers/create', 'CustomerController@create');
    //save new customer
    Route::post('/customers', 'CustomerController@store');
    // show customer edit page
    Route::get('/customers-edit/{id}','CustomerController@edit');
    // show details of specific customer
    Route::get('/customers/show/{id}', 'CustomerController@show');
    // update customer
    Route::patch('/customers/{customer}', 'CustomerController@update');
    // check customer code of prospect
    Route::post('/check-customer-code', 'CustomerController@checkCustomerCode');
    // Delete Customer
    Route::delete('/customers/{customer}', 'CustomerController@destroy');
    // Get customers address geocode
    Route::get('/customers-geocode/{address}', 'CustomerController@getGeocode');

    //Customer Classfication
    // show customer classfication page
    Route::get('/customers-classification', 'CustomerClassificationController@index')->name('classification_list');
    // fetch all customer classfication
    Route::get('/customers-classification-all', 'CustomerClassificationController@indexData');

    
    //Messages
    Route::get('/messages', 'MessageController@index')->name('messages_list');
    //save new message
    Route::post('/messages', 'MessageController@store');
    // fetch all message
    Route::get('/messages-all', 'MessageController@indexData');
    // fetch message by user
    Route::get('/messages-specific/{id}', 'MessageController@messageByuser');
    // fetch all recipients
    Route::get('/recipients', 'MessageController@recipients');

    //Schedules
    // Fetch todays schedule(Customer,event,mapping)
    Route::get('/schedules-todays', 'ScheduleController@todays');
    // Fetch all todays schedule
    Route::get('/ schedules-todays-all', 'ScheduleController@todaysAll');
    // Fetch all todays schedule per user
    Route::get('/schedules-user-today', 'ScheduleController@todayByUser');

    //Attendances
    // Fetch all visiting area
    Route::get('/attendances-visiting', 'AttendanceReportController@visiting');
    // Fetch all completed visiting area
    Route::get('/attendances-completed', 'AttendanceReportController@completed');

    // Expenses
    Route::get('/expenses', 'ExpenseController@indexExpense');
    // Get all expenses
    Route::get('/expenses-all', 'ExpenseController@indexExpenseData');
    // Show Expense Report page
    Route::get('/expenses-report', 'ExpenseController@index');
    // Fetch expense report by date
    Route::post('/expense-report-bydate', 'ExpenseController@generateBydate');
    // Fetch expense report by date per user
    Route::get('/expense-report-bydate-peruser/{ids}', 'ExpenseController@generateBydatePerUser');
    // Fetch expense report by date
    Route::get('/expense-report/{id}', 'ExpenseController@show');
    // Add Expenses
    Route::post('/expenses', 'ExpenseController@store');
    // Update Expenses
    Route::patch('/expenses/{expensesType}', 'ExpenseController@update');


    // Expense Unposted
    Route::get('/expense-unposted', 'ExpenseController@expenseUnPostedIndex');
    Route::post('/expense-unposteds', 'ExpenseController@expenseUnPostedIndexData');
});

// AP Routes
Route::group(['middleware' => ['auth', 'role:ap|tax|audit']], function(){
    // Payments
    Route::get('/payments', 'PaymentController@index');
    // Store payment expense
    Route::post('/payments', 'PaymentController@store');
    // Fetch expense report by company
    Route::post('/expense-by-company', 'ExpenseController@generateByCompany');
    // Get expenses submitted
    Route::post('/expense-submitted', 'ExpenseController@getExpenseSubmitted');
    // Show expenses submitted page
    Route::get('/expense-submitted-page', 'ExpenseController@showExpenseSubmitted')->name('expense_submitted_list');
    // Simulate expenses submitted
    Route::get('/expense-simulate/{id}', 'ExpenseController@simulateExpenseSubmitted');


    // Expense Posted
    Route::get('/expense-posted', 'ExpenseController@expensePostedIndex');
    Route::post('/expense-posteds', 'ExpenseController@expensePostedIndexData');


    // Show SAP user page
    Route::get('/sap/account', 'SapUserController@index')->name('sap_user');
    // Show SAP user page
    Route::get('/sap/accounts', 'SapUserController@indexData');
    // Store new sap account
    Route::post('/sap/account', 'SapUserController@store');
    // Update sap account
    Route::patch('/sap/account/{sapUser}', 'SapUserController@update');
    // Delete sap account
    Route::delete('/sap/account/{sapUser}', 'SapUserController@destroy');

});

// Hr routes
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|hr|tax|audit']], function () {
    // Attendance Report
    Route::get('/attendance-report', 'AttendanceReportController@index')->name('report_list');
    // fetch all Attendance Report
    Route::get('/attendance-report-all', 'AttendanceReportController@indexData');
    // fetch attendance report by date
    Route::post('/attendance-report-bydate', 'AttendanceReportController@generateBydate');

    // Companies
    Route::get('/companies', 'CompanyController@index');
    // Add company  
    Route::post('/company', 'CompanyController@store');
    // Update company
    Route::patch('/company/{company}', 'CompanyController@update');
    // Delete company
    Route::delete('/company/{id}', 'CompanyController@destroy');

    // Locations
    Route::get('/locations', 'LocationController@index');
});

//Audit routes
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|hr|tax|audit']], function () {
    //Schedules
    Route::get('/schedules', 'ScheduleController@index');
    Route::get('/schedules/{date_from}/{date_to}', 'ScheduleController@indexData');

    Route::get('/schedule-customer/{classification}', 'ScheduleController@scheduleCustomerData');
});



// Request Routes
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|approver']], function () {

    // Request to close a visit
    Route::get('/request-close','CloseVisitController@index');
    // Request schedules
    Route::get('/change-schedule', 'ScheduleController@changeScheduleIndex');
    // Fetch all companies
    Route::post('/change-schedule-bydate', 'ScheduleController@changeScheduleIndexData');
    // Disapproved request schedules
    Route::post('/change-schedule-disapproved', 'ScheduleController@changeScheduleDisapproved');
});


Route::group(['middleware' => ['auth', 'role:it']], function () {
    Route::get('/internal-order', 'SalesmanInternalOrderController@index')->name('internal-order');
    Route::post('/internal-order', 'SalesmanInternalOrderController@store');
    Route::patch('/internal-order/{salesmanInternalOrder}', 'SalesmanInternalOrderController@update');
    Route::delete('/internal-order/{salesmanInternalOrder}', 'SalesmanInternalOrderController@destroy');
    Route::get('/internal-orders', 'SalesmanInternalOrderController@indexData')->name('internal-order');
});


// Fetch all sap servers
Route::get('/sap/server', 'SapServerController@index');

//Auth role
Route::get('/auth-role', 'UserController@getRole');

Route::get('/visited-customer', 'CustomerController@customerVisitedIndex');
Route::post('/visited-customer-all', 'CustomerController@customerVisitedIndexData');