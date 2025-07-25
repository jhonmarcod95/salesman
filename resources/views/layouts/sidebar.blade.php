<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ url('/home') }}">
            <img src="{{ url(Auth::user()->companies->pluck('company_logo')[0]) }}" class="navbar-brand-img" alt="...">&nbsp; Salesforce
        </a>

        {{--Mobile Navigation--}}
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
              <span class="avatar avatar-sm rounded-circle">
                <img alt="Image placeholder" src="{{ url('img/theme/team-4-800x800.jpg') }}">
              </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome!</h6>
                    </div>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>My profile</span>
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>

                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>

        </ul>


        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="./index.html">
                            <img src="{{ url('img/brand/PFMC.jpg') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="Search" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->
{{--            @if(!Auth::user()->hasRole('hr') && !Auth::user()->hasRole('ap') && !Auth::user()->hasRole('approver') && !Auth::user()->hasRole('tax'))--}}
            @if(!Auth::user()->hasRole(['hr','ap','approver','tax', 'audit']))
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/home') }}">
                            <i class="ni ni-tv-2 text-primary"></i> Dashboard
                        </a>
                    </li>

                    @if(!Auth::user()->hasRole(['finance-gl']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/schedules') }}">
                            <i class="ni ni-calendar-grid-58 text-orange"></i> Schedules
                        </a>
                    </li>
                    @endif
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('/announcements') }}">
                            <i class="ni ni-notification-70 text-purple"></i> Announcements
                        </a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('/users') }}">
                            <i class="ni ni-single-02 text-yellow"></i> Users
                        </a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('/messages') }}">
                            <i class="ni ni-email-83 text-green"></i> Messages
                        </a>
                    @if($notification > 0)
                        <span style="display:inline-block; background: red; position: relative; top: -47px; left: 137px; border-radius: 10px; color: #fff; min-width: 20px; text-align: center;">{{ $notification }}</span>
                    @endif
                    </li> --}}

                    <li>
                        <a data-toggle="collapse" href="#Report" class="collapsed" aria-expanded="false">
                            <div class="nav-link">
                                <i class="ni ni-ruler-pencil text-pink"></i>
                                <span>Report</span>
                            </div>
                        </a>
                        <div class="collapse space-left" id="Report" style="">
                            <ul class="nav" style="list-style-type: none;">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/attendance-report') }}">
                                        <i class="ni ni-books text-gray"></i> Attendance Report
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/expenses-report') }}">
                                        <i class="ni ni-collection text-indigo"></i> Expense Report
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/rejected-expenses-report') }}">
                                        <i class="fa fa-window-close text-danger"></i> Rejected Expense Monitoring
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/visited-customer') }}">
                                        <i class="ni ni-spaceship text-indigo"></i>Visited Customer
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/sales-report') }}">
                                        <i class="ni ni-book-bookmark text-green"></i> Sales Report
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/appointment-duration-report') }}">
                                        <i class="ni ni-watch-time text-primary"></i> Appointment Duration Report
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/surveys') }}">
                                        <i class="ni ni-chart-bar-32  text-warning"></i> Survey Report
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/individual_performance') }}">
                                        <i class="ni ni-circle-08 text-danger"></i> Individual Performance
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a data-toggle="collapse" href="#Map" class="collapsed" aria-expanded="false">
                                        <div class="nav-link">
                                            <i class="ni ni-square-pin text-blue"></i>
                                            <span>Map</span>
                                        </div>
                                    </a>

                                    <div class="collapse space-left" id="Map" style="">
                                            <ul class="nav" style="list-style-type: none;">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ url('/map-analytics-report-user') }}">
                                                        <i class="ni ni-pin-3 text-green"></i> Users
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ url('/map-analytics-report-customer') }}">
                                                        <i class="ni ni-pin-3 text-orange"></i> Customers
                                                    </a>
                                                </li>
                                            </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @if(!Auth::user()->hasRole(['manager', 'finance-gl']))
                    <li>
                        <a data-toggle="collapse" href="#MasterData" class="collapsed" aria-expanded="false">
                            <div class="nav-link">
                                <i class="ni ni-folder-17"></i>
                                <span>Master Data</span>
                            </div>
                        </a>
                        <div class="collapse space-left" id="MasterData" style="">
                            <ul class="nav" style="list-style-type: none;">
                                <li>
                                    <a class="nav-link" href="{{ url('/tsr') }}">
                                        <i class="ni ni-circle-08 text-pink"></i> Salesman
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/customers') }}">
                                        <i class="ni ni-shop text-blue"></i> Customers
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/expenses') }}">
                                        <i class="ni ni-money-coins text-yellow"></i> Expenses
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/surveys/display') }}">
                                        <i class="ni ni-paper-diploma text-green"></i> Survey
                                    </a>
                                </li>
                                @if (Auth::user()->hasRole(['it']))
                                <li>
                                    <a class="nav-link" href="{{ url('version-release/main') }}">
                                        <i class="ni ni-settings-gear-65 text-gray"></i> Version Release
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/forced-close') }}">
                                        <i class="ni ni-calendar-grid-58 text-orange"></i> Force Close Schedule
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @endif
                    @if(Auth::user()->hasRole(['manager', 'it']))
                    <li>
                        <a data-toggle="collapse" href="#Support" class="collapsed" aria-expanded="false">
                            <div class="nav-link">
                                <i class="ni ni-settings-gear-65 text-gray"></i>
                                <span>Support</span>
                            </div>
                        </a>
                        <div class="collapse space-left" id="Support" style="">
                            <ul class="nav" style="list-style-type: none;">
                                <li>
                                    @if(Auth::user()->hasRole(['it', 'finance-gl']))
                                    <a class="nav-link" href="{{ url('/internal-order') }}">
                                        <i class="ni ni-money-coins text-indigo"></i> Internal Order
                                    </a>
                                    @endif
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('posting-error') }}">
                                        <i class="fa fa-window-close text-danger"></i> Posting Errors
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('no-voucher-payment') }}">
                                        <i class="ni ni-money-coins text-gray"></i> No Voucher Payments
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/expense-unposted') }}">
                                        <i class="ni ni-chart-pie-35 text-danger"></i> Unposted Expenses
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/run-command') }}">
                                        <i class="ni ni-tv-2 text-primary"></i> Run Command
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>
            @else
            <ul class="navbar-nav">
                @if(Auth::user()->hasRole(['hr','audit']))
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/attendance-report') }}">
                        <i class="ni ni-books text-gray"></i> Attendance Report
                    </a>
                </li>
                @endif
                @if(Auth::user()->hasRole('ap'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/payments') }}">
                        <i class="ni ni-briefcase-24 text-yellow"></i> Payment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/expense-posted') }}">
                        <i class="ni ni-basket text-orange"></i> Posted Expenses
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/users') }}">
                        <i class="ni ni-user-run text-blue"></i> Users
                    </a>
                </li>

                <li>
                    <a class="nav-link" href="{{ url('/tsr') }}">
                        <i class="ni ni-circle-08 text-pink"></i> Salesman
                    </a>
                </li>
                @endif
            </ul>
            @endif
            @if(Auth::user()->hasRole(['tax','audit', 'finance-gl']))
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/expense-posted') }}">
                        <i class="ni ni-basket text-orange"></i> Posted Expenses
                    </a>
                </li>
            </ul>
            @endif
            <ul class="navbar-nav">
                @if(Auth::user()->hasRole('audit'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/schedules') }}">
                            <i class="ni ni-calendar-grid-58 text-orange"></i> Schedules
                        </a>
                    </li>
                @endif
                @if(Auth::user()->level() > 5 || Auth::user()->level() == 3 || Auth::user()->hasRole('coordinator-2'))
                    <li>
                        <a data-toggle="collapse" href="#Request" class="collapsed" aria-expanded="false">
                            <div class="nav-link">
                                <i class="ni ni-book-bookmark text-red"></i>
                                <span>Request</span>
                            </div>
                        </a>
                        <div class="collapse space-left" id="Request" style="">
                            <ul class="nav" style="list-style-type: none;">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/change-schedule') }}">
                                        <i class="ni ni-books text-gray"></i> Change Schedule
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/request-close') }}">
                                        <i class="ni ni-books text-gray"></i> Close visit requests
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
            
        </div>
    </div>
</nav>