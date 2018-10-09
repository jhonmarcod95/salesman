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

//Tsr
Route::get('/tsr', 'TsrController@index');
Route::get('/tsr/create', 'TsrController@create');
Route::get('/tsr/show/{id}', 'TsrController@show');

Route::post('/schedules/store', 'TsrController@store');
Route::patch('/schedules/update/{id}', 'TsrController@update');
Route::delete('/schedules/destroy/{id}', 'TsrController@destroy');

//User
Route::get('/users', 'UserController@index');
Route::get('/users/create', 'UserController@create');
Route::get('/users/show/{id}', 'UserController@show');

Route::post('/users/store', 'UserController@store');
Route::patch('/users/update/{id}', 'UserController@update');
Route::delete('/users/destroy/{id}', 'UserController@destroy');

//Customer
Route::post('/customers', 'CustomerController@store');
Route::get('/customers', 'CustomerController@index')->name('customers_list');
Route::get('/edit-customers/{id}','CustomerController@edit');
Route::get('/customers/show/{id}', 'CustomerController@show');
Route::patch('/customers/{id}', 'CustomerController@update');
Route::get('/customers/create', 'CustomerController@create');

Route::delete('/customers/destroy/{id}', 'CustomerController@destroy');


// Provinces
Route::get('/provinces', 'ProvinceController@index');

// Regions
Route::get('/regions', 'RegionController@index');
