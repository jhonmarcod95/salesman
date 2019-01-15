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
            @if(!Auth::user()->hasRole('hr') && !Auth::user()->hasRole('ap'))
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/home') }}">
                            <i class="ni ni-tv-2 text-primary"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/schedules') }}">
                            <i class="ni ni-calendar-grid-58 text-orange"></i> Schedules
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/announcements') }}">
                            <i class="ni ni-notification-70 text-purple"></i> Announcements
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('/users') }}">
                            <i class="ni ni-single-02 text-yellow"></i> Users
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/messages') }}">
                            <i class="ni ni-email-83 text-green"></i> Messages
                        </a>
                    @if($notification > 0)
                        <span style="display:inline-block; background: red; position: relative; top: -47px; left: 137px; border-radius: 10px; color: #fff; min-width: 20px; text-align: center;">{{ $notification }}</span>
                    @endif
                    </li>

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
                            </ul>
                        </div>
                    </li>

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
                                        <i class="ni ni-circle-08 text-pink"></i> TSR
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="{{ url('/companies') }}">
                                        <i class="ni ni-istanbul text-green"></i> Company
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
                            </ul>
                        </div>
                    </li>
                    @if(Auth::user()->level() > 5)
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
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>
            @else
            <ul class="navbar-nav">
                @if(Auth::user()->hasRole('hr'))
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
                @endif
            </ul>
            @endif
            {{--<!-- Divider -->--}}
            {{--<hr class="my-3">--}}
            {{--<!-- Heading -->--}}
            {{--<h6 class="navbar-heading text-muted">Documentation</h6>--}}
            {{--<!-- Navigation -->--}}
            {{--<ul class="navbar-nav mb-md-3">--}}
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/getting-started/overview.html">--}}
                        {{--<i class="ni ni-spaceship"></i> Getting started--}}
                    {{--</a>--}}
                {{--</li>--}}
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/foundation/colors.html">--}}
                        {{--<i class="ni ni-palette"></i> Foundation--}}
                    {{--</a>--}}
                {{--</li>--}}
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/components/alerts.html">--}}
                        {{--<i class="ni ni-ui-04"></i> Components--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--</ul>--}}
        </div>
    </div>
</nav>