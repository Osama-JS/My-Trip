{{-- Header start --}}
<style>
/* Premium Language Switcher */
.lang-switcher-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #fa1600 0%, #ff4d3d 100%);
    border-radius: 12px;
    color: #fff !important;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(250, 22, 0, 0.3);
}
.lang-switcher-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(250, 22, 0, 0.4);
}
.lang-dropdown {
    min-width: 200px;
    padding: 0;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}
.lang-dropdown-header {
    background: linear-gradient(135deg, #fa1600 0%, #ff4d3d 100%);
    color: #fff;
    padding: 12px 16px;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.lang-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none !important;
    transition: all 0.2s ease;
    color: #333 !important;
}
.lang-option:hover {
    background: #f8f9fa;
}
.lang-option.active {
    background: linear-gradient(135deg, rgba(250, 22, 0, 0.1) 0%, rgba(255, 77, 61, 0.1) 100%);
}
.lang-option i.fa-globe {
    font-size: 1.2rem;
    color: #fa1600;
}
.lang-option .lang-info {
    flex: 1;
}
.lang-option .lang-name {
    font-weight: 600;
    display: block;
}
.lang-option .lang-code {
    font-size: 0.75rem;
    color: #999;
}
.lang-option .lang-check {
    color: #fa1600;
}
[dir="rtl"] .lang-option {
    flex-direction: row-reverse;
}
[dir="rtl"] .lang-option .lang-info {
    text-align: right;
}
[dir="rtl"] .lang-dropdown-header {
    flex-direction: row-reverse;
}
</style>

<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page-title', 'Dashboard')
                    </div>
                </div>
                <div class="nav-item d-flex align-items-center">
                    <div class="input-group search-area">
                        <input type="text" class="form-control" placeholder="{{ __('Search...') }}">
                        <span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    {{-- Language Switcher - Icon Only --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link lang-switcher-btn" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <i class="fa fa-globe"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end lang-dropdown">
                            <div class="lang-dropdown-header">
                                <i class="fa fa-language"></i>
                                <span>{{ __('Select Language') }}</span>
                            </div>
                            <a href="{{ route('lang.switch', 'en') }}" class="lang-option {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                <i class="fa fa-globe"></i>
                                <div class="lang-info">
                                    <span class="lang-name">English</span>
                                    <span class="lang-code">EN</span>
                                </div>
                                @if(app()->getLocale() == 'en')
                                    <i class="fa fa-check lang-check"></i>
                                @endif
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="lang-option {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                <i class="fa fa-globe"></i>
                                <div class="lang-info">
                                    <span class="lang-name">العربية</span>
                                    <span class="lang-code">AR</span>
                                </div>
                                @if(app()->getLocale() == 'ar')
                                    <i class="fa fa-check lang-check"></i>
                                @endif
                            </a>
                        </div>
                    </li>

                    {{-- Notifications --}}
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link" href="javascript:void(0);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26.309" height="23.678" viewBox="0 0 26.309 23.678">
                          <path id="Path_1955" data-name="Path 1955" d="M163.217,78.043a7.409,7.409,0,0,1,10.5-10.454l.506.506.507-.506a7.409,7.409,0,0,1,10.5,10.454L175.181,88.686a1.316,1.316,0,0,1-1.912,0Zm11.008,7.823,9.1-9.632.027-.027a4.779,4.779,0,1,0-6.759-6.757l-1.435,1.437a1.317,1.317,0,0,1-1.861,0l-1.437-1.437a4.778,4.778,0,0,0-6.758,6.757l.026.027Z" transform="translate(-161.07 -65.42)" fill="#135846" fill-rule="evenodd"/>
                        </svg>
                        </a>
                    </li>
                    {{-- Profile Dropdown --}}
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->profile_photo_url }}" width="20" alt=""/>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                                <svg id="icon-user2" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ms-2">{{ __('Profile') }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span class="ms-2">{{ __('Logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
{{-- Header end --}}
