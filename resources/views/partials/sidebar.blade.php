{{-- Sidebar with Normal Dropdowns and Icons --}}
<div class="dlabnav">
    <div class="dlabnav-scroll">
        {{-- Floating Scroll Buttons (Only visible in horizontal mode) --}}
        <button class="nav-control-btn prev" id="nav-prev-btn" type="button">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="nav-control-btn next" id="nav-next-btn" type="button">
            <i class="fa fa-chevron-right"></i>
        </button>
        
        <div class="nav-scroll-wrapper" id="nav-scroll-container">
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

            {{-- Flights --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-plane"></i>
                    <span class="nav-text">{{ __('Flights') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.flights.index') }}"><i class="fa fa-list"></i>{{ __('Flight Bookings') }}</a></li>
                    <li><a href="{{ route('admin.bookings.flights.requests') }}"><i class="fa fa-paper-plane"></i>{{ __('Requests') }}</a></li>
                </ul>
            </li>

            {{-- Hotels --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-hotel"></i>
                    <span class="nav-text">{{ __('Hotels') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.bookings.hotels.index') }}"><i class="fa fa-list"></i>{{ __('Hotel Bookings') }}</a></li>
                    <li><a href="{{ route('admin.bookings.hotels.requests') }}"><i class="fa fa-concierge-bell"></i>{{ __('Requests') }}</a></li>
                </ul>
            </li>

            {{-- Tour Packages (Trips) --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-map"></i>
                    <span class="nav-text">{{ __('Tour Packages') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.trips.index') }}"><i class="fa fa-suitcase"></i>{{ __('Manage Tours') }}</a></li>
                    <li><a href="{{ route('admin.trip-categories.index') }}"><i class="fa fa-tags"></i>{{ __('Categories') }}</a></li>
                    <li><a href="{{ route('admin.trip-bookings.index') }}"><i class="fa fa-calendar-check"></i>{{ __('Tour Bookings') }}</a></li>
                </ul>
            </li>

            {{-- Companies --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-briefcase"></i>
                    <span class="nav-text">{{ __('Companies') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.companies.index') }}"><i class="fa fa-building"></i>{{ __('Manage Companies') }}</a></li>
                    <li><a href="{{ route('admin.companycodes.index') }}"><i class="fa fa-barcode"></i>{{ __('Company Codes') }}</a></li>
                </ul>
            </li>


            {{-- Locations --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-location"></i>
                    <span class="nav-text">{{ __('Locations') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.countries.index') }}"><i class="fa fa-globe"></i>{{ __('Countries') }}</a></li>
                    <li><a href="{{ route('admin.cities.index') }}"><i class="fa fa-city"></i>{{ __('Cities') }}</a></li>
                </ul>
            </li>


            {{-- Financial Management --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-layer-1"></i>
                    <span class="nav-text">{{ __('Financial Management') }}</span>
                </a>
                <ul aria-expanded="false">
                   <li><a href="{{ route('admin.payments.index') }}">{{ __('Payment Records') }}</a></li>
                    <li><a href="{{ route('admin.bank-transfers.index') }}">{{ __('Bank Transfer Review') }}</a></li>
                </ul>
            </li>

            {{-- Security & Access --}}
            @can('view users')
            <li class="mega-menu-container">
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
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

            {{-- ===== الإعدادات والإدارة ===== --}}
            <li>
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings-2"></i>
                    <span class="nav-text">{{ __('Settings & Administration') }}</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-bell"></i>{{ __('Notifications') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.questions.index') }}">
                            <i class="fa fa-question-circle"></i>{{ __('Questions') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}">
                            <i class="flaticon-381-settings-2"></i>{{ __('Platform Settings') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.banners.index') }}">
                            <i class="fa fa-image"></i>{{ __('Banners') }}
                        </a>
                    </li>
                </ul>
            </li>


        </div>

        <div class="copyright text-center">
            <p><strong>Fly Vio</strong> © {{ date('Y') }}</p>
        </div>
    </div>
</div>

{{-- Sidebar end --}}
