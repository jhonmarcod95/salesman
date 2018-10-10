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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

//Schedules
Route::get('/schedules', 'ScheduleController@index');

Route::post('/schedules/store', 'ScheduleController@store');
Route::patch('/schedules/update/{id}', 'ScheduleController@update');
Route::patch('/schedules/change/{id}', 'ScheduleController@change');
Route::delete('/schedules/destroy/{id}', 'ScheduleController@destroy');

Route::post('/schedules/store', 'TsrController@store');
Route::patch('/schedules/update/{id}', 'TsrController@update');
Route::delete('/schedules/destroy/{id}', 'TsrController@destroy');

//User
// show user page
Route::get('/users', 'UserController@index');
// fetch all user
Route::get('/users-all', 'UserController@indexData');
//show add user page
Route::get('/users/create', 'UserController@create');
//add new user
Route::post('/user', 'UserController@store');


Route::get('/users/show/{id}', 'UserController@show');
Route::patch('/users/update/{id}', 'UserController@update');
Route::delete('/users/destroy/{id}', 'UserController@destroy');

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
Route::patch('/tsr/{customer}', 'TsrController@update');
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


// Provinces
Route::get('/provinces', 'ProvinceController@index');

// Regions
Route::get('/regions', 'RegionController@index');
