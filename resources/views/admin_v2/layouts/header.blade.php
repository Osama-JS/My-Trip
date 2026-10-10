<header class="admin-header">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <!-- Mobile Sidebar Toggle Button -->
        <button type="button" class="btn-icon d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Navigation">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Live Global Search Bar -->
        <div class="header-search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="globalSearchInput" data-search-url="{{ route('admin.global-search') }}" autocomplete="off" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث عن حجز، عميل، رحلة، أو قائمة (Ctrl+K)...' : 'Search bookings, customers, tours, or menus (Ctrl+K)...' }}">
            <i id="searchSpinner" class="fa-solid fa-circle-notch fa-spin text-primary d-none" style="position: absolute; inset-inline-end: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
            
            <!-- Dynamic Search Dropdown Results Container -->
            <div class="search-results-dropdown" id="globalSearchResults"></div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="header-actions">
        <!-- Switch to Legacy Admin Button -->
        <a href="{{ route('admin.switch-version', 'v1') }}" class="btn-icon" style="width: auto; padding: 0 14px; gap: 6px; font-weight: 700; font-size: 0.8rem; border-radius: 9999px; text-decoration: none;" title="{{ app()->getLocale() == 'ar' ? 'الرجوع للوحة السابقة' : 'Switch to Legacy Admin' }}">
            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
            <span class="d-none d-md-inline">{{ app()->getLocale() == 'ar' ? 'اللوحة السابقة' : 'Legacy Admin' }}</span>
        </a>

        <!-- Language Switcher -->
        <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}">
            <span style="font-weight: 800; font-size: 0.8rem;">{{ app()->getLocale() == 'ar' ? 'EN' : 'عربي' }}</span>
        </a>

        <!-- Dark / Light Theme Toggle -->
        <button type="button" class="btn-icon" id="themeToggleBtn" title="{{ app()->getLocale() == 'ar' ? 'تبديل المظهر' : 'Toggle Theme' }}">
            <i class="fa-solid fa-moon"></i>
        </button>

        <!-- Fullscreen Toggle -->
        <button type="button" class="btn-icon d-none d-sm-inline-flex" id="fullscreenBtn" title="{{ app()->getLocale() == 'ar' ? 'شاشة كاملة' : 'Fullscreen' }}">
            <i class="fa-solid fa-expand"></i>
        </button>

        <!-- Notifications Dropdown -->
        <a href="{{ route('admin.notifications.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'الإشعارات' : 'Notifications' }}">
            <i class="fa-regular fa-bell"></i>
            <span class="badge-dot"></span>
        </a>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="user-dropdown" id="userProfileDropdownBtn" aria-expanded="false" role="button" tabindex="0">
                <div class="user-avatar">
                    @if(auth()->user() && auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div class="user-info d-none d-md-flex">
                    <span class="user-name">{{ auth()->user()->name ?? (auth()->user()->first_name . ' ' . auth()->user()->last_name) }}</span>
                    <span class="user-role">{{ optional(optional(auth()->user())->roles)->first()->name ?? optional(auth()->user())->user_type ?? (app()->getLocale() == 'ar' ? 'مدير النظام' : 'Administrator') }}</span>
                </div>
                <i class="fa-solid fa-chevron-down ms-1 text-xs text-muted"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" style="border-radius: var(--radius-lg); min-width: 230px; background: var(--bg-card); border: 1px solid var(--border-color) !important; overflow: hidden; z-index: 2050;">
                <li class="p-3 border-bottom" style="border-color: var(--border-light) !important; background: var(--bg-hover);">
                    <h6 class="mb-0 font-bold" style="color: var(--text-main); font-size: 0.9rem;">{{ auth()->user()->name ?? (auth()->user()->first_name . ' ' . auth()->user()->last_name) }}</h6>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">{{ auth()->user()->email }}</small>
                </li>
                <li class="py-1">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-circle text-primary" style="width: 18px;"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'الملف الشخصي' : 'Profile' }}</span>
                    </a>
                </li>
                <li class="py-1">
                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-gear text-secondary" style="width: 18px;"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'الإعدادات العامة' : 'Settings' }}</span>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-0" style="border-color: var(--border-light) !important;"></li>
                <li class="p-1">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger border-0 bg-transparent w-100 d-flex align-items-center gap-2 rounded-md">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="width: 18px;"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Logout' }}</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
