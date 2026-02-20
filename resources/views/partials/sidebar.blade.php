{{-- Sidebar with Normal Dropdowns and Icons --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            {{-- Main Menu --}}
            <li>
                <a href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">{{ __('Dashboard') }}</span>
                </a>
            </li>


            <li>
                <a href="{{ route('admin.subscribers.index') }}" aria-expanded="false">
                    <i class="fa fa-users"></i>
                    <span class="nav-text">{{ __('Subscribers') }}</span>
                </a>
            </li>
             {{-- Booking Management --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-solid fa-plane"></i>
                    <span class="nav-text">{{ __('Flights') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.index') }}"><i class="fa fa-database "></i>{{ __('All Bookings') }}</a></li>
                    <li><a href="{{ route('admin.bookings.flights.available') }}"><i class="fa fa-search "></i>{{ __('Search Flights') }}</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-solid fa-hotel"></i>
                    <span class="nav-text">{{ __('Hotels') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.hotels.index') }}"><i class="fa fa-hotel "></i>{{ __('Hotels List') }}</a></li>
                    <li><a href="{{ route('admin.bookings.hotels.requests') }}"><i class="fa fa-concierge-bell "></i>{{ __('Requests') }}</a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-solid fa-hotel"></i>
                    <span class="nav-text">{{ __('Locations') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.countries.index') }}"><i class="fa fa-hotel "></i>{{ __('Country') }}</a></li>
                    <li><a href="{{ route('admin.cities.index') }}"><i class="fa fa-concierge-bell "></i>{{ __('Cities') }}</a></li>
                </ul>
            </li>

            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-solid fa-hotel"></i>
                    <span class="nav-text">{{ __('Company') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.companies.index') }}"><i class="fa fa-hotel "></i>{{ __('Company') }}</a></li>
                    <li><a href="{{ route('admin.companycodes.index') }}"><i class="fa fa-concierge-bell "></i>{{ __('CompanyCode') }}</a></li>
                </ul>
            </li>




            {{-- Security & Access --}}
            @can('view users')
            <li class="mega-menu-container">
                <a  class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-lock"></i>
                    <span class="nav-text nav-text-arrow">
                        {{ __('Access Control') }}
                    </span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.users.index') }}"><i class="fa fa-users "></i>{{ __('Users') }}</a></li>
                    <li><a href="{{ route('admin.roles.index') }}"><i class="fa fa-user-shield "></i>{{ __('Roles') }}</a></li>
                    @can('view permissions')
                    <li><a href="{{ route('admin.permissions.index') }}"><i class="fa fa-key "></i>{{ __('Permissions') }}</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            <li>
                <a href="{{ route('profile.edit') }}" aria-expanded="false">
                    <i class="fa fa-user-circle"></i>
                    <span class="nav-text">{{ __('My Profile') }}</span>
                </a>
            </li>

            {{-- Settings --}}
            <li>
                <a href="{{ route('admin.settings.index') }}" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">{{ __('Platform Settings') }}</span>
                </a>
            </li>

            {{-- Settings --}}
            <li>
                <a href="{{ route('admin.questions.index') }}" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">{{ __('Question') }}</span>
                </a>
            </li>
            {{-- Reports & Logs --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-043-menu"></i>
                    <span class="nav-text">{{ __('System Reports') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.reports.api_logs') }}"><i class="fa fa-code "></i>{{ __('API Logs') }}</a></li>
                    <li><a href="{{ route('admin.reports.search_logs') }}"><i class="fa fa-chart-bar "></i>{{ __('Search Statistics') }}</a></li>
                </ul>
            </li>
        </ul>

        <div class="copyright text-center">
            <p><strong>My Trip</strong> © {{ date('Y') }}</p>
        </div>
    </div>
</div>
{{-- Sidebar end --}}
