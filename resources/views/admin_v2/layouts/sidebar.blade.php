<aside class="admin-sidebar">
    <!-- Sidebar Header / Brand Logo -->
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-plane-departure"></i>
            <span>{{ config('app.name', 'My-Trip') }} <small style="font-size: 0.65rem; background: var(--primary-light); color: var(--primary); padding: 2px 6px; border-radius: 4px; font-weight: 800;">v2</small></span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <ul class="sidebar-menu">
        <!-- 1. Main Section -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Main' }}</li>
        
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="fa-solid fa-chart-pie menu-icon text-primary"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/subscribers*') ? 'active' : '' }}">
            <a href="{{ route('admin.subscribers.index') }}" class="menu-link">
                <i class="fa-solid fa-users menu-icon text-info"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'المشتركين والعملاء' : 'Subscribers' }}</span>
                <span class="menu-badge badge-info">{{ \App\Models\User::count() }}</span>
            </a>
        </li>

        <!-- 2. Bookings & Travel Services -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'خدمات السفر والحجوزات' : 'Travel & Bookings' }}</li>

        <!-- Flights (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/bookings/flights*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-plane menu-icon text-primary"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'الطيران' : 'Flights' }}</span>
                <span class="menu-badge badge-primary me-2">{{ \App\Models\Booking::count() }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.bookings.flights.index') }}" class="menu-link {{ request()->routeIs('admin.bookings.flights.index') ? 'active' : '' }}"><i class="fa-solid fa-list me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الطيران' : 'Flight Bookings' }}</a></li>
                <li><a href="{{ route('admin.bookings.flights.analytics') }}" class="menu-link {{ request()->routeIs('admin.bookings.flights.analytics') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الطيران' : 'Flight Analytics' }}</a></li>
                <li><a href="{{ route('admin.bookings.flights.profits') }}" class="menu-link {{ request()->routeIs('admin.bookings.flights.profits') ? 'active' : '' }}"><i class="fa-solid fa-dollar-sign text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الطيران' : 'Flight Profits' }}</a></li>
            </ul>
        </li>

        <!-- Hotels (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/bookings/hotels*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-hotel menu-icon text-success"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'الفنادق' : 'Hotels' }}</span>
                <span class="menu-badge badge-success me-2">{{ \App\Models\HotelBooking::count() }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.bookings.hotels.index') }}" class="menu-link {{ request()->routeIs('admin.bookings.hotels.index') ? 'active' : '' }}"><i class="fa-solid fa-list me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الفنادق' : 'Hotel Bookings' }}</a></li>
                <li><a href="{{ route('admin.bookings.hotels.profits') }}" class="menu-link {{ request()->routeIs('admin.bookings.hotels.profits') ? 'active' : '' }}"><i class="fa-solid fa-dollar-sign text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الفنادق' : 'Hotel Profits' }}</a></li>
                <li><a href="{{ route('admin.bookings.hotels.analytics') }}" class="menu-link {{ request()->routeIs('admin.bookings.hotels.analytics') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'التحليلات' : 'Analytics' }}</a></li>
            </ul>
        </li>

        <!-- Tour Packages (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/trips*') || request()->is('admin/trip-*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-umbrella-beach menu-icon text-warning"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'الباقات والجولات' : 'Tour Packages' }}</span>
                <span class="menu-badge badge-warning me-2">{{ \App\Models\TripBooking::count() }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.trips.index') }}" class="menu-link"><i class="fa-solid fa-suitcase me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الجولات' : 'Manage Tours' }}</a></li>
                <li><a href="{{ route('admin.trip-categories.index') }}" class="menu-link"><i class="fa-solid fa-tags me-1"></i> {{ app()->getLocale() == 'ar' ? 'التصنيفات' : 'Categories' }}</a></li>
                <li><a href="{{ route('admin.trip-bookings.index') }}" class="menu-link"><i class="fa-solid fa-calendar-check me-1"></i> {{ app()->getLocale() == 'ar' ? 'حجوزات الجولات' : 'Tour Bookings' }}</a></li>
                <li><a href="{{ route('admin.trips.analytics') }}" class="menu-link"><i class="fa-solid fa-chart-pie me-1"></i> {{ app()->getLocale() == 'ar' ? 'تحليلات الجولات' : 'Tour Analytics' }}</a></li>
                <li><a href="{{ route('admin.trips.profits') }}" class="menu-link"><i class="fa-solid fa-dollar-sign text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح الجولات' : 'Tour Profits' }}</a></li>
            </ul>
        </li>

        <!-- Travel Insurance (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/insurance*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-shield-halved menu-icon text-info"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'التأمين السياحي' : 'Travel Insurance' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.insurance.index') }}" class="menu-link"><i class="fa-solid fa-list me-1"></i> {{ app()->getLocale() == 'ar' ? 'وثائق التأمين' : 'Insurance Policies' }}</a></li>
                <li><a href="{{ route('admin.insurance.profits') }}" class="menu-link"><i class="fa-solid fa-dollar-sign text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'أرباح التأمين' : 'Insurance Profits' }}</a></li>
                <li><a href="{{ route('admin.insurance.settings') }}" class="menu-link"><i class="fa-solid fa-cog me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات التأمين' : 'Insurance Settings' }}</a></li>
            </ul>
        </li>

        <!-- 3. Companies & B2B -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'الشركات والوجهات' : 'Companies & Locations' }}</li>

        <!-- Companies (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/companies*') || request()->is('admin/companycodes*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-briefcase menu-icon text-primary"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'الشركات (B2B)' : 'Companies' }}</span>
                <span class="menu-badge badge-primary me-2">{{ \App\Models\Company::count() }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.companies.index') }}" class="menu-link"><i class="fa-solid fa-building me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الشركات' : 'Manage Companies' }}</a></li>
                <li><a href="{{ route('admin.companycodes.index') }}" class="menu-link"><i class="fa-solid fa-barcode me-1"></i> {{ app()->getLocale() == 'ar' ? 'أكواد الشركات' : 'Company Codes' }}</a></li>
            </ul>
        </li>

        <!-- Locations (Dropdown) -->
        <li class="menu-item has-submenu {{ request()->is('admin/countries*') || request()->is('admin/cities*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-location-dot menu-icon text-danger"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'المواقع والوجهات' : 'Locations' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.countries.index') }}" class="menu-link"><i class="fa-solid fa-globe me-1"></i> {{ app()->getLocale() == 'ar' ? 'الدول' : 'Countries' }}</a></li>
                <li><a href="{{ route('admin.cities.index') }}" class="menu-link"><i class="fa-solid fa-city me-1"></i> {{ app()->getLocale() == 'ar' ? 'المدن' : 'Cities' }}</a></li>
            </ul>
        </li>

        <!-- 4. Financial Management -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'الإدارة المالية' : 'Financial Management' }}</li>

        <li class="menu-item has-submenu {{ request()->is('admin/payments*') || request()->is('admin/bank-*') || request()->is('admin/wallets*') || request()->is('admin/support*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-layer-group menu-icon text-success"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'العمليات المالية' : 'Finance Operations' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.payments.index') }}" class="menu-link"><i class="fa-solid fa-money-bill-wave me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجلات الدفع' : 'Payment Records' }}</a></li>
                <li><a href="{{ route('admin.bank-accounts.index') }}" class="menu-link"><i class="fa-solid fa-university me-1"></i> {{ app()->getLocale() == 'ar' ? 'الحسابات البنكية' : 'Bank Accounts' }}</a></li>
                <li>
                    <a href="{{ route('admin.bank-transfers.index') }}" class="menu-link">
                        <i class="fa-solid fa-right-left me-1"></i> {{ app()->getLocale() == 'ar' ? 'مراجعة التحويلات البنكية' : 'Bank Transfers' }}
                        @php $pendingTransfers = \App\Models\BankTransfer::where('status', 'pending')->count(); @endphp
                        @if($pendingTransfers > 0)
                            <span class="badge-v2 badge-warning ms-auto" style="font-size: 0.65rem;">{{ $pendingTransfers }}</span>
                        @endif
                    </a>
                </li>
                <li><a href="{{ route('admin.wallets.index') }}" class="menu-link"><i class="fa-solid fa-wallet me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة المحافظ' : 'Wallets Management' }}</a></li>
                <li>
                    <a href="{{ route('admin.support.index') }}" class="menu-link">
                        <i class="fa-solid fa-headset me-1"></i> {{ app()->getLocale() == 'ar' ? 'تذاكر الدعم الفني' : 'Support Tickets' }}
                        @php $openTickets = \App\Models\SupportTicket::where('status', 'open')->count(); @endphp
                        @if($openTickets > 0)
                            <span class="badge-v2 badge-danger ms-auto" style="font-size: 0.65rem;">{{ $openTickets }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        <!-- 5. Access Control & Security -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'الأمان والصلاحيات' : 'Access & Security' }}</li>

        <li class="menu-item has-submenu {{ request()->is('admin/users*') || request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-lock menu-icon text-warning"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'التحكم بالوصول' : 'Access Control' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.users.index') }}" class="menu-link"><i class="fa-solid fa-users me-1"></i> {{ app()->getLocale() == 'ar' ? 'المستخدمين' : 'Users' }}</a></li>
                <li><a href="{{ route('admin.roles.index') }}" class="menu-link"><i class="fa-solid fa-user-shield me-1"></i> {{ app()->getLocale() == 'ar' ? 'الأدوار' : 'Roles' }}</a></li>
                <li><a href="{{ route('admin.permissions.index') }}" class="menu-link"><i class="fa-solid fa-key me-1"></i> {{ app()->getLocale() == 'ar' ? 'الصلاحيات' : 'Permissions' }}</a></li>
            </ul>
        </li>

        <!-- 6. Reports & System Logs -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'التقارير وسجلات النظام' : 'Reports & Logs' }}</li>

        <li class="menu-item has-submenu {{ request()->is('admin/reports*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-chart-column menu-icon text-primary"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'تقارير وسجلات النظام' : 'System Reports' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.reports.api_logs') }}" class="menu-link"><i class="fa-solid fa-code me-1"></i> {{ app()->getLocale() == 'ar' ? 'سجلات الـ API Logs' : 'API Logs' }}</a></li>
                <li><a href="{{ route('admin.reports.search_logs') }}" class="menu-link"><i class="fa-solid fa-chart-bar me-1"></i> {{ app()->getLocale() == 'ar' ? 'إحصائيات البحث' : 'Search Statistics' }}</a></li>
            </ul>
        </li>

        <!-- 7. System Health & Maintenance -->
        <li class="menu-item has-submenu {{ request()->is('admin/system*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-heart-pulse menu-icon text-danger"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'حالة النظام والمراقبة' : 'System & Maintenance' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.system.health') }}" class="menu-link"><i class="fa-solid fa-server text-success me-1"></i> {{ app()->getLocale() == 'ar' ? 'مركز العمليات والصحة' : 'Health & Operations Hub' }}</a></li>
                <li><a href="{{ route('admin.system.status') }}" class="menu-link"><i class="fa-solid fa-tower-broadcast text-info me-1"></i> {{ app()->getLocale() == 'ar' ? 'حالة النظام والخدمات المباشرة' : 'Live System Status' }}</a></li>
            </ul>
        </li>

        <!-- 8. Platform Administration & Settings -->
        <li class="menu-category">{{ app()->getLocale() == 'ar' ? 'الإعدادات والإدارة' : 'Settings & Admin' }}</li>

        <li class="menu-item has-submenu {{ request()->is('admin/settings*') || request()->is('admin/pages*') || request()->is('admin/questions*') || request()->is('admin/commissions*') || request()->is('admin/banners*') ? 'open active' : '' }}">
            <a class="menu-link">
                <i class="fa-solid fa-sliders menu-icon text-secondary"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'إعدادات المنصة' : 'Settings & Admin' }}</span>
                <i class="fa-solid fa-chevron-left menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.pages.index') }}" class="menu-link"><i class="fa-solid fa-file-lines me-1"></i> {{ app()->getLocale() == 'ar' ? 'إدارة الصفحات' : 'Manage Pages' }}</a></li>
                <li><a href="{{ route('admin.questions.index') }}" class="menu-link"><i class="fa-solid fa-circle-question me-1"></i> {{ app()->getLocale() == 'ar' ? 'الأسئلة الشائعة' : 'Questions' }}</a></li>
                <li><a href="{{ route('admin.settings.index') }}" class="menu-link"><i class="fa-solid fa-gear me-1"></i> {{ app()->getLocale() == 'ar' ? 'إعدادات المنصة' : 'Platform Settings' }}</a></li>
                <li><a href="{{ route('admin.commissions.index') }}" class="menu-link"><i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ app()->getLocale() == 'ar' ? 'عمولات المنصة' : 'Platform Commissions' }}</a></li>
                <li><a href="{{ route('admin.banners.index') }}" class="menu-link"><i class="fa-solid fa-image me-1"></i> {{ app()->getLocale() == 'ar' ? 'البانرات الإعلانية' : 'Banners' }}</a></li>
            </ul>
        </li>

        <!-- Switch to Legacy Admin Option -->
        <li style="margin-top: 1.5rem; padding: 0.5rem 0.75rem;">
            <a href="{{ url('/admin/dashboard') }}" class="menu-link" style="border: 1px dashed var(--border-color); justify-content: center; font-size: 0.8rem; color: var(--text-light);">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                <span>{{ app()->getLocale() == 'ar' ? 'التبديل للوحة الكلاسيكية' : 'Switch to Classic v1' }}</span>
            </a>
        </li>
    </ul>
</aside>
