<?php
use App\CustomerOrder;
use App\TsrSapCustomer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

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

Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

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
    // fetch allowed companies to view / filter
    Route::get('/companies-allowed', function (){
        return \Illuminate\Support\Facades\Auth::user()->companies;
    });
});

//Surver Creation
Route::post('surveys/create','SurveysController@createQuestionnaire');
Route::get('surveys/display','SurveysController@displayQuestionnaire');
Route::post('surveys/fetch','SurveysController@fetchQuestionnaire');
Route::post('surveys/display','SurveysController@dataQuestionnaire');
Route::post('surveys/edit-questionnaire','SurveysController@editQuestionnaire');

// Admin Routes
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|approver|tax|finance-gl']], function () {
    //Schedules
    Route::get('/schedules', 'ScheduleController@index');
    Route::get('/schedules/{date_from}/{date_to}', 'ScheduleController@indexData');

    Route::get('/schedule-customer/{classification}', 'ScheduleController@scheduleCustomerData');

    Route::post('/schedules/store', 'ScheduleController@store')->middleware('permission:create.schedule');
    Route::patch('/schedules/update/{id}', 'ScheduleController@update')->middleware('permission:create.schedule');
    Route::patch('/schedules/change/{id}', 'ScheduleController@change')->middleware('permission:create.schedule');
    Route::delete('/schedules/destroy/{id}', 'ScheduleController@destroy')->middleware('permission:create.schedule');

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
    //selection user
    Route::get('/selection-users', 'UserController@selectionUsers');


    Route::get('/selection-users/show/{id}', 'UserController@show');
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
    // Route::get('/expenses-report', 'ExpenseController@index');

    Route::get('/historical-expenses-report', 'ExpenseController@historicalExpenseReport');
    Route::get('/historical-expenses-report-data', 'ExpenseController@historicalExpenseReportData');

    Route::get('/expenses-top-spender-report', 'ExpenseController@expenseTopSpender');
    Route::post('/expenses-top-spender-data', 'ExpenseController@expenseTopSpenderData');
    Route::get('/expenses-current-top-spender-data', 'ExpenseController@expenseCurrentTopSpenderData');

    //DMS Received Expense
    Route::group(['prefix' => 'dms-received-expense'], function () {
        Route::get('/', 'ExpenseController@dmsReceivedReportIndex');
        Route::get('/all', 'ExpenseController@dmsReceivedReportAll');
        Route::get('/not-received-expense', 'ExpenseController@dmsPendingReceivedReportAll');
        Route::get('/no-claimed-expenses', 'ExpenseController@noClaimedExpenses');
        Route::get('/export', 'ExpenseController@exportDmsReport');
    });

    //Expense v2
    Route::group(['prefix' => '/expenses-report'], function() {
        Route::get('/', 'ExpenseController@index');
        Route::get('/all', 'ExpenseController@getExpensePerUser');
        Route::get('/verified-stat', 'ExpenseController@getExpenseVerifiedStat');
        Route::get('/expenses/{user_id}', 'ExpenseController@show2');
        Route::get('/export', 'ExpenseController@export');
    });

    Route::get('/expense-io-report', 'ExpenseController@expenseIOReport');
    Route::post('/expense-io-report-data', 'ExpenseController@expenseIOReportData');

    //Checker
    Route::get('/expense-io-checker', 'ExpenseController@expenseIOChecker');

    // Fetch expense report by date per user
    Route::get('/expense-report-bydate-peruser/{ids}', 'ExpenseController@generateBydatePerUser');
    // Fetch expense report by date
    Route::get('/expense-report/{id}', 'ExpenseController@show'); //TODO: Remove
    // Add Expenses
    Route::post('/expenses', 'ExpenseController@store');
    // Update Expenses
    Route::patch('/expenses/{expensesType}', 'ExpenseController@update');
    // Verify expense attachment
    Route::post('/verify-expense-attachment/{id}', 'ExpenseController@verifyAttachment');
    Route::get('/expense-verification-statuses', 'ExpenseController@getExpenseVerificationStatuses');
    Route::get('/expense-rejected-remarks', 'ExpenseController@getExpenseRejectedRemarks');


    // Expense Unposted
    Route::get('/expense-unposted', 'ExpenseController@expenseUnPostedIndex');
    Route::post('/expense-unposteds', 'ExpenseController@expenseUnPostedIndexData');


    //Map Analytic Report
    Route::get('/map-analytics-report', 'MapAnalyticsReportController@index');
    Route::get('/map-year', 'MapAnalyticsReportController@getYear');

    Route::get('/map-analytics-report-user', 'MapAnalyticsReportController@mapUsers');

    Route::get('/map-analytics-report-customer', 'MapAnalyticsReportController@mapCustomers');

    //Get Map Customers Data 
    Route::post('/users-data', 'MapAnalyticsReportController@usersData');
    
    Route::get('/schedule-types-all', 'MapAnalyticsReportController@scheduleTypes');
    
    Route::get('/customers-all', 'MapAnalyticsReportController@customersData');
    
    
    Route::post('/user-locations', 'MapAnalyticsReportController@userLocations');

    Route::get('/map-users-all', 'MapAnalyticsReportController@users');

    Route::post('/customer-locations', 'MapAnalyticsReportController@customerLocations');

    //Customer Visits
    Route::get('/customer-visits/{customer_code}', 'MapAnalyticsReportController@customerVisits');

    //Survey Route Setup
    Route::get('/surveys','SurveysController@index');

});

Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|tsr']], function () {
    Route::get('/surveys/home','SurveysController@surveyHome');
});


// For AAPC Survey
Route::group(['middleware' => ['auth', 'role:it|tsr|coordinator|manager|vp|president']], function () {

    Route::get('/aapc-farmer','AapcFarmerMeetingController@index');
    Route::get('/aapc-farmer/create','AapcFarmerMeetingController@create');

});



// AP Routes
Route::group(['middleware' => ['auth', 'role:ap|tax|audit|finance-gl']], function(){
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
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|hr|tax|audit|finance-gl']], function () {
    // Attendance Report
    Route::get('/attendance-report', 'AttendanceReportController@index')->name('report_list');
    // fetch all Attendance Report
    Route::get('/attendance-report-all', 'AttendanceReportController@indexData');
    // fetch attendance report by date
    Route::post('/attendance-report-bydate', 'AttendanceReportController@generateBydate');
    Route::get('/attendance-report-today', 'AttendanceReportController@generateByToday');

    // fetch searched TSR
    Route::get('/tsr-filter', 'AttendanceReportController@generateBydate');

    // fetch export data
    Route::post('/fetch-export', 'AttendanceReportController@exportData');

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
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|ap|hr|tax|audit|finance-gl']], function () {
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


Route::group(['middleware' => ['auth', 'role:it|finance-gl']], function () {
    Route::get('/internal-order', 'SalesmanInternalOrderController@index')->name('internal-order');
    Route::post('/internal-order', 'SalesmanInternalOrderController@store');
    Route::patch('/internal-order/{salesmanInternalOrder}', 'SalesmanInternalOrderController@update');
    Route::delete('/internal-order/{salesmanInternalOrder}', 'SalesmanInternalOrderController@destroy');
    Route::get('/internal-orders', 'SalesmanInternalOrderController@indexData')->name('internal-order');
});

//Customer Master Role
Route::group(['middleware' => ['auth', 'role:it|president|evp|vp|avp|coordinator|manager|customer-master|finance-gl']], function () {
    //Customer
    // show customer page
    Route::get('/customers', 'CustomerController@index')->name('customers_list');
    // fetch all customer
    
    Route::get('/customers-all', 'CustomerController@indexData');

    Route::post('/customers-all-filter', 'CustomerController@indexDataFilter');

    Route::post('/change-verified-status/{customer}', 'CustomerController@changeVerifiedStatus');

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

    Route::get('/customers-geocode-json/{address}', 'CustomerController@getGeocodeCustomer');

    Route::get('/customer-details/{customer}', 'CustomerController@getCustomerDetails');

    //Customer Classfication
    // show customer classfication page
    Route::get('/customers-classification', 'CustomerClassificationController@index')->name('classification_list');

    // fetch all customer classfication
    Route::get('/customers-classification-all', 'CustomerClassificationController@indexData');

    Route::get('/customers-status-options', 'CustomerClassificationController@statusData');
    Route::get('/customers-classification-options', 'CustomerClassificationController@classificationData');
});




// Fetch all sap servers
Route::get('/sap/server', 'SapServerController@index');

//Auth role
Route::get('/auth-role', 'UserController@getRole');

Route::get('/visited-customer', 'CustomerController@customerVisitedIndex');
Route::get('/visited-customer-today', 'CustomerController@customerVisitedToday');
Route::post('/visited-customer-all', 'CustomerController@customerVisitedIndexData');

Route::get('/sales-report', 'CustomerController@customerSalesReport');

Route::post('/sales-report-customer-data', 'CustomerController@customersSalesReportData');

Route::post('/sales-report-customer-visit-data', 'CustomerController@customersSalesReportData');

Route::get('/appointment-duration-report', 'CustomerController@customerAppointmentDurationReport');

Route::post('/appointment-duration-report-data', 'CustomerController@customerAppointmentDurationReportData');


//Dashboard
Route::get('/schedule-for-visit', 'HomeController@scheduleForVisit');

Route::get('/monthly-actual-visited', 'HomeController@monthlyActualVisited');

Route::get('/most-customer-visit', 'HomeController@mostCustomerVisits');

Route::get('/work-start-time', 'HomeController@workStartEndTime');

Route::get('/total-travel-time', 'HomeController@totalTimeTravel');

Route::get('/monthly-total-travel-time', 'HomeController@monthlyTotalTimeTravel');


Route::post('/save-dashboard-filter', 'HomeController@saveDashboardFilter');

Route::get('/get-dashboard-filter', 'HomeController@getDashboardFilter');

Route::get('/get-sap-tsr', 'HomeController@getSapTsr');
Route::get('/get-sap-tsr-actual-expense', 'HomeController@getSapTsrActualExpense');

Route::get('/customer-visited-per-area', 'HomeController@customerVisiterPerArea');

Route::get('/customer-schedule-per-area', 'HomeController@customerSchedulePerArea');

Route::get('/individual_performance', 'IndividualPerformanceController@index');

Route::get('/individual-performance-data', 'IndividualPerformanceController@indexData');

Route::post('/individual-performance-filter-data', 'IndividualPerformanceController@indexFilterData');

Route::get('/tsr-get-last-visited/{attendance_id}/{user_id}', 'CustomerController@getLastVisitedDate');


Route::get('/year-options', 'HomeController@yearOptions');

Route::get('/customer-codes', 'CustomerController@getCustomerCodes');

Route::get('/customer-codes-all', 'CustomerController@getCustomerCodesAll');

Route::get('/missed_itineraries', 'ScheduleController@missedItineraries');
Route::post('/missed-itineraries-data', 'ScheduleController@missedItinerariesData');

Route::get('/change-planned-schedules-data', 'ScheduleController@changePlannedSchedulesData');
Route::get('/change_planned_schedules', 'ScheduleController@changePlannedSchedules');

Route::get('/virtual-schedule-report', 'ScheduleController@virtualScheduleReport');
Route::get('/virtual-schedule-report-data-today', 'ScheduleController@virtualScheduleReportDataToday');
Route::post('/virtual-schedule-report-data-filter', 'ScheduleController@virtualScheduleReportDataFilter');

//Get SAP Customer with sales
Route::get('/get-sap-customer', function () {

    $client = new Client();

    $connection_pfmc = [
        'ashost' => '172.17.1.35',
        'sysnr' => '02',
        'client' => '888',
        'user' => 'rfidproject',
        'passwd' => 'P@ssw0rd4',
    ];

    $customers = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                            ['query' => 
                                ['connection' => $connection_pfmc,
                                    'table' => [
                                        'table' => ['KNVP' => 'customers_tsr'],
                                        'fields' => [
                                            'KUNNR' => 'customer_code',
                                            'KUNN2' => 'tsr_customer_code',
                                            'VKORG' => 'sales_organization',
                                            'VTWEG' => 'common_division',
                                            'SPART' => 'division',
                                            'PARVW' => 'partner_function',
                                        ],
                                        'options' => [
                                            ['TEXT' => "PARVW ='Z1' OR "],
                                            ['TEXT' => "PARVW ='Z3' OR "],
                                            ['TEXT' => "PARVW ='ZS'"],
                                        ]
                                    ]
                                ]
                            ],
                            ['timeout' => 60],
                            ['delay' => 10000]
                        );

    $customers_data = json_decode($customers->getBody(), true);
    
    if($customers_data){
        $x = 0;
        foreach($customers_data as $k => $data){
            $tsr_customer_arr = [];
            $tsr_customer_arr['server'] = 'PFMC';
          
            $validate = TsrSapCustomer::where('customer_code',$data['customer_code'])
                            ->where('tsr_customer_code',$data['tsr_customer_code'])
                            ->where('sales_organization',$data['sales_organization'])
                            ->where('common_division',$data['common_division'])
                            ->where('division',$data['division'])
                            ->where('partner_function',$data['partner_function'])
                            ->first();
                            
            if(empty($validate)){
                $tsr_customer_arr['customer_code'] = $data['customer_code'];
                $tsr_customer_arr['tsr_customer_code'] = $data['tsr_customer_code'];
                $tsr_customer_arr['sales_organization'] = $data['sales_organization'];
                $tsr_customer_arr['common_division'] = $data['common_division'];
                $tsr_customer_arr['division'] = $data['division'];
                $tsr_customer_arr['partner_function'] = $data['partner_function'];
                TsrSapCustomer::create($tsr_customer_arr);
                $x++;
            }
           
        }
        return $x;
    }

});

//Get SAP Customer with sales PFMC
Route::get('/get-all-customer-pfmc', function () {

    $client = new Client();

    $connection_pfmc = [
        'ashost' => '172.17.1.35',
        'sysnr' => '02',
        'client' => '888',
        'user' => 'rfidproject',
        'passwd' => 'P@ssw0rd4',
    ];
    $connection_lfug = [
        'ashost' => '172.17.2.36',
        'sysnr' => '00',
        'client' => '888',
        'user' => 'rfidproject',
        'passwd' => 'P@ssw0rd4'
    ];

    $tsr_pfmc = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                    ['query' => 
                        ['connection' => $connection_pfmc,
                            'table' => [
                                'table' => ['KNA1' => 'tsr'],
                                'fields' => [
                                    'KUNNR' => 'tsr_number',
                                    'NAME1' => 'name',
                                    'NAME2' => 'name_2',
                                ],
                                // 'options' => [
                                //     ['TEXT' => "NAME2 ='Technical Sales Representative'"]
                                // ]
                            ]
                        ]
                    ],
                    ['timeout' => 60],
                    ['delay' => 10000]
                );

    $tsr_pfmc_data = json_decode($tsr_pfmc->getBody(), true); 

    return $tsr_pfmc_data;

});

//Get SAP Customer with sales LFUG
Route::get('/get-all-customer-lfug', function () {

    $client = new Client();

    $connection_lfug = [
        'ashost' => '172.17.2.36',
        'sysnr' => '00',
        'client' => '888',
        'user' => 'rfidproject',
        'passwd' => 'P@ssw0rd4'
    ];

    $tsr_lfug = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                    ['query' => 
                        ['connection' => $connection_lfug,
                            'table' => [
                                'table' => ['KNA1' => 'tsr'],
                                'fields' => [
                                    'KUNNR' => 'tsr_number',
                                    'NAME1' => 'name',
                                    'NAME2' => 'name_2',
                                ],
                                // 'options' => [
                                //     ['TEXT' => "NAME2 ='Technical Sales Representative'"]
                                // ]
                            ]
                        ]
                    ],
                    ['timeout' => 60],
                    ['delay' => 10000]
                );

    $tsr_lfug_data = json_decode($tsr_lfug->getBody(), true); 

    return $tsr_lfug_data;

});

//Get SAP Customer with sales LFUG
Route::get('/get-all-customer-hana', function () {

    $client = new Client();

    $connection = [
        'ashost' => '172.17.28.19',
        'sysnr' => '01',
        'client' => '888',
        'user' => 'app_user',
        'passwd' => '{iamprogrammer}'
    ];


    $hana = $client->request('GET', 'http://10.97.70.51:8012/api/read-table',
                    ['query' => 
                        ['connection' => $connection,
                            'table' => [
                                'table' => ['KNA1' => 'do_headers'],
                                'fields' => [
                                    'KUNNR' => 'customer_code',
                                    'NAME1' => 'name',
                                    'STRAS' => 'street',
                                    'ORT01' => 'city',
                                    'KTOKD' => 'account_group'
                                ],
                            ]
                        ]
                    ],
                    ['timeout' => 60],
                    ['delay' => 10000]
                );

    $hana_data = json_decode($hana->getBody(), true); 

    return $hana_data;

});


