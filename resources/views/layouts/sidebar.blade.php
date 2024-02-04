<div id="sideBar" class="d-print-none"
     @if(env('APP_DEBUG'))
         style="background-color: lightgreen;"
     @endif
>
    <div id="sideButton">
        <i class="bi bi-arrow-left-short"></i>
    </div>
    <div id="sideBarLogo" class="logo">
        <img src="{{asset('img/logo/logomini.png')}}" alt="" class="me-2" height="35">
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="{{ route('dashboard.index') }}"
               class="nav-link
                   @if (Route::currentRouteName() == 'dashboard.index')
                    active
                   @endif">
                <i class="bi bi-speedometer"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('profile.index', ['user' => Auth::user()->id]) }}"
               class="nav-link
                   @if (Route::currentRouteName() == 'profile.index')
                    active
                   @endif">
                <i class="bi bi-person-circle"></i>
                Profile
            </a>
        </li>
        <li>
            <div class="btn-group dropend w-100">
                <span type="button"
                    class="dropdown-toggle nav-link w-100
                        @if (Route::currentRouteName() == 'leave.request' ||
                             Route::currentRouteName() == 'leave.history' ||
                             Route::currentRouteName() == 'administration.leave-type')
                            active
                        @endif"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar-day-fill"></i>
                    Leave Module
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('leave.request', ['user' => Auth::user()->id]) }}">
                            Apply Leave
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leave.history', ['user' => Auth::user()->id]) }}">
                            Leave History
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('administration.leave-type') }}">
                            View Leave Type
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <div class="btn-group dropend w-100">
                <span type="button"
                    class="dropdown-toggle nav-link w-100
                        @if (Route::currentRouteName() == 'leave.redeem-index' ||
                             Route::currentRouteName() == 'leave.redeem-history')
                            active
                        @endif"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar2-plus-fill"></i>
                    Redeem Leave
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('leave.redeem-index') }}">
                            Redeem Leave
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leave.redeem-history') }}">
                            Leave Redemption History
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <div class="btn-group dropend w-100">
                <span type="button" data-bs-toggle="dropdown" aria-expanded="false"
                      class="dropdown-toggle nav-link w-100
                      @if (Route::currentRouteName() == 'eform.travel.index'
                             || Route::currentRouteName() == 'eform.apply'
                             )
                        active
                      @endif">

                    <i class="bi bi-clipboard-data-fill"></i>
                    E-Form
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('eform.apply') }}">
                            Apply EForm</a>
                    </li>
                    <li>
                        <a href="{{ route('eform.travel.index', ['user' => Auth::user()->id] ) }}">
                            E-form History</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <div class="btn-group dropend w-100">
                <span type="button" data-bs-toggle="dropdown" aria-expanded="false"
                      class="dropdown-toggle nav-link w-100
                          @if (Route::currentRouteName() == 'leave.approve' ||
                               Route::currentRouteName() == 'leave.redeem-approve' ||
                               Route::currentRouteName() == 'eform.approvers'
                                )
                                active
                          @endif">
                    <i class="bi bi-clipboard-check-fill"></i>
                    Approvers
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('leave.approve', ['user' => Auth::user()->id] ) }}">
                            Review Leave
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leave.redeem-approve') }}">
                            Review Redemption Leave
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('eform.approvers') }}">
                            Review Eform
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <div class="btn-group dropend w-100">
                <span type="button" data-bs-toggle="dropdown" aria-expanded="false"
                      class="dropdown-toggle nav-link w-100
                        @if (Route::currentRouteName() == 'employee.index' ||
                             Route::currentRouteName() == 'administration.approvers' ||
                             Route::currentRouteName() == 'leave.summary' ||
                             Route::currentRouteName() == 'administration.updateAnnualLeave' ||
                             Route::currentRouteName() == 'leave.redeem-summary' ||
                             Route::currentRouteName() == 'eform.summary' ||
                             Route::currentRouteName() == 'dashboard.calendar'
                             )
                            active
                        @endif">
                    <i class="bi bi-building-fill"></i>
                    Administration
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('employee.index') }}">Search Employees</a>
                    </li>
                    <li>
                        <a href="{{ route('administration.approvers') }}">Assign Approvers</a>
                    </li>
                    <li>
                        <a href="{{ route('leave.summary', ['user' => Auth::user()->id]) }}">Company Leave Summary</a>
                    </li>
                    <li>
                        <a href="{{ route('leave.redeem-summary')}}">
                            Leave Redemption Summary
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('eform.summary') }}">
                            E-Form Summary
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('administration.updateAnnualLeave') }}">Update Leave Balance</a>
                    </li>
                    <li>
                        <a href="{{ route('administration.deductLeave') }}">Deduct Leave Balance</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.calendar')}}">Leave Request Calendar</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a href="{{ route('user-guide')}}"
               class="nav-link
                   @if (Route::currentRouteName() == 'user-guide')
                    active
                   @endif">
                <i class="bi bi-file-earmark-text-fill"></i>
                User Guide
            </a>
        </li>

    </ul>
    <a class="log-out nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-door-open-fill"></i>
        Sign out
    </a>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf <!-- Add CSRF token for security -->
    </form>
</div>


