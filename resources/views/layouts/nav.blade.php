<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block">{{ session('header_text') }}</a>

        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <li class="nav-item dropdown">
                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                          <img alt="Image placeholder" src="{{ url('img/theme/team-4-800x800.jpg') }}">
                        </span>
                        <div class="media-body ml-2 d-none d-lg-block">
                            <span class="mb-0 text-sm  font-weight-bold">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome!</h6>
                    </div>
                    <a href="{{ route('change_password') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>Change Password</span>
                    </a>
                    @if(Auth::user()->hasRole('ap'))
                        <a href="{{ route('sap_user') }}" class="dropdown-item">
                            <i class="ni ni-single-02"></i>
                            <span>SAP User</span>
                        </a>
                    @endif


                    <div class="dropdown-divider"></div>
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
    </div>
</nav>