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

// Authenticated Routes
Route::group(['middleware' => 'auth'], function(){
    // Provinces
    Route::get('/provinces', 'ProvinceController@index');

    // Regions
    Route::get('/regions', 'RegionController@index');

    //Roles
    Route::get('/roles', 'RoleController@index');
});

// Admin Routes
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    //Schedules
    Route::get('/schedules', 'ScheduleController@index');
    Route::get('/schedules/{date_from}/{date_to}', 'ScheduleController@indexData');

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
    // show details of specific tsr
    Route::get('/tsr/show/{id}', 'TsrController@show');

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

    Route::delete('/customers/destroy/{id}', 'CustomerController@destroy');

    //Customer Classfication
    // show customer classfication page
    Route::get('/customers-classification', 'CustomerClassificationController@index')->name('classification_list');
    // fetch all customer classfication
    Route::get('/customers-classification-all', 'CustomerClassificationController@indexData');

});

// Admin Routes
Route::group(['middleware' => ['auth', 'role:admin|user']], function () {
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
});