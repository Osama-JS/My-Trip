{{-- Header start --}}
<style>
/* Smart Search Premium Styles - Global Unified Search */
.header .search-area {
    position: relative;
    max-width: 500px !important;
}
.header-results-container {
    position: absolute !important;
    top: 100% !important;
    right: 0 !important;
    left: auto !important;
    width: 450px !important;
    background: #ffffff !important;
    border-radius: 16px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18) !important;
    margin-top: 12px !important;
    z-index: 2000000 !important;
    border: 1px solid #e2e8f0 !important;
    max-height: 550px !important;
    overflow-y: auto !important;
    display: none;
    padding: 0 !important;
    direction: rtl !important;
    text-align: right !important;
    scrollbar-width: thin;
}
.header-results-container.show {
    display: block !important;
    animation: premiumSlideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes premiumSlideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.search-section {
    padding: 10px 0;
}
.search-section:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
}
.search-header {
    font-size: 11px !important;
    text-transform: uppercase !important;
    color: #94a3b8 !important;
    font-weight: 800 !important;
    padding: 12px 20px 8px !important;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.search-header i {
    font-size: 10px;
}

.header-search-item {
    display: flex !important;
    align-items: center !important;
    padding: 12px 20px !important;
    text-decoration: none !important;
    color: #1e293b !important;
    transition: all 0.2s ease !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.header-search-item:hover, .header-search-item.active {
    background: #f8fafc !important;
    color: #fa1600 !important;
}
.header-search-item .icon-wrapper {
    width: 40px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 16px !important;
    color: #fa1600 !important;
    background: #fff5f5 !important;
    border-radius: 12px !important;
    margin-left: 15px !important;
    flex-shrink: 0 !important;
    transition: all 0.2s;
}
.header-search-item:hover .icon-wrapper, .header-search-item.active .icon-wrapper {
    background: #fa1600 !important;
    color: #fff !important;
    transform: scale(1.05);
}
.header-search-item .item-content {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
}
.header-search-item .item-title {
    font-weight: 700 !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.header-search-item .item-meta {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 2px !important;
}
.header-search-item .badge-status {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
    margin-right: auto;
    font-weight: 600;
}

/* Spinner */
.search-loader {
    padding: 20px;
    text-align: center;
    color: #fa1600;
}

/* Language Switcher */
.lang-switcher-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: #f8fafc;
    border-radius: 12px;
    color: #fa1600 !important;
    transition: all 0.2s;
}
.lang-switcher-btn:hover {
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
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
                
                {{-- Smart Global Search --}}
                <div class="nav-item d-flex align-items-center">
                    <div class="input-group search-area">
                        <input type="text" id="admin-menu-search" class="form-control" placeholder="{{ __('Search for bookings, users, or menus...') }}" autocomplete="off">
                        <span class="input-group-text">
                            <i id="search-spinner" class="fas fa-circle-notch fa-spin d-none me-2 text-primary"></i>
                            <span class="badge badge-sm light border text-muted d-none d-md-inline me-2" style="font-size: 10px;">Ctrl + K</span>
                            <a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a>
                        </span>
                        
                        <div id="search-results-dropdown" class="header-results-container"></div>
                    </div>
                </div>

                <ul class="navbar-nav header-right">
                    <li class="nav-item dropdown">
                        <a class="nav-link lang-switcher-btn" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <i class="fa fa-globe"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end lang-dropdown">
                            <a href="{{ route('lang.switch', 'en') }}" class="dropdown-item lang-option {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                <i class="fa fa-flag-usa me-2"></i> English
                            </a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="dropdown-item lang-option {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                <i class="fa fa-flag me-2"></i> العربية
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link position-relative" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            @if($unreadNotificationsCount > 0)
                                <span class="badge light text-white bg-primary rounded-circle position-absolute" style="top: 2px; right: 2px; font-size: 10px; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0 border-0 shadow-lg" style="width: 350px; border-radius: 12px; overflow: hidden;">
                            <div class="dropdown-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white mb-0">{{ __('Notifications') }}</h6>
                            </div>
                            <div class="widget-media dlab-scroll p-2" style="max-height: 380px; overflow-y: auto;">
                                <ul class="timeline">
                                    @forelse($headerNotifications as $notification)
                                        <li class="p-2 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}" style="border-radius: 8px; margin-bottom: 5px;">
                                            <a href="{{ route('customer.notifications.index') }}" class="d-flex text-decoration-none">
                                                <div class="timeline-panel d-flex align-items-center w-100">
                                                    <div class="media me-3 p-2 bg-{{ $notification->is_read ? 'secondary' : 'primary' }} rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-{{ $notification->icon ?? 'bell' }} fa-sm"></i>
                                                    </div>
                                                    <div class="media-body flex-grow-1">
                                                        <h6 class="mb-1 text-dark" style="font-size: 0.85rem; font-weight: 600;">{{ $notification->title }}</h6>
                                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">{{ \Illuminate\Support\Str::limit($notification->content, 45) }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <div class="text-center p-4"><p class="text-muted mb-0">{{ __('No new notifications') }}</p></div>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->profile_photo_url }}" width="20" alt=""/>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                                <span class="ms-2">{{ __('Profile') }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item ai-icon"><span class="ms-2">{{ __('Logout') }}</span></button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('admin-menu-search');
        const resultsDropdown = document.getElementById('search-results-dropdown');
        const spinner = document.getElementById('search-spinner');
        let selectedIndex = -1;
        let searchTimeout = null;

        // Events
        searchInput.addEventListener('focus', showDefaultResults);
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            
            // 1. Initial Local Search (Menus)
            handleLocalSearch(query);

            // 2. Debounced Backend Search (Global)
            clearTimeout(searchTimeout);
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => handleBackendSearch(query), 400);
            }
        });
        
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
                resultsDropdown.classList.remove('show');
            }
        });

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); searchInput.focus(); }
            if (resultsDropdown.classList.contains('show')) {
                const items = resultsDropdown.querySelectorAll('.header-search-item');
                if (e.key === 'ArrowDown') { e.preventDefault(); selectedIndex = (selectedIndex + 1) % items.length; highlightItem(items); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); selectedIndex = (selectedIndex - 1 + items.length) % items.length; highlightItem(items); }
                else if (e.key === 'Enter' && selectedIndex > -1) { e.preventDefault(); items[selectedIndex].click(); }
                else if (e.key === 'Escape') { resultsDropdown.classList.remove('show'); searchInput.blur(); }
            }
        });

        function showDefaultResults() {
            const recent = getRecentPages();
            if (recent.length > 0) {
                renderAllResults({ menus: recent }, "{{ __('Recent Pages') }}");
            }
        }

        function handleLocalSearch(query) {
            if (!query) { showDefaultResults(); return; }
            if (!window.adminMenus) return;
            const filtered = window.adminMenus.filter(m => m.searchable.toLowerCase().includes(query)).slice(0, 5);
            renderAllResults({ menus: filtered });
        }

        async function handleBackendSearch(query) {
            spinner.classList.remove('d-none');
            try {
                const response = await fetch(`{{ route('admin.global-search') }}?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                // Get current local menu results to merge
                const menuQuery = searchInput.value.trim().toLowerCase();
                const menus = window.adminMenus ? window.adminMenus.filter(m => m.searchable.toLowerCase().includes(menuQuery)).slice(0, 5) : [];
                
                renderAllResults({
                    menus: menus,
                    bookings: data.bookings || [],
                    users: data.users || [],
                    trips: data.trips || []
                });
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                spinner.classList.add('d-none');
            }
        }

        function renderAllResults(data, customHeader = null) {
            selectedIndex = -1;
            let html = '';
            let hasAny = false;

            // Helper to render section
            const renderSection = (title, items, icon) => {
                if (!items || items.length === 0) return '';
                hasAny = true;
                let sectionHtml = `<div class="search-section">
                    <div class="search-header"><i class="${icon}"></i> ${title}</div>`;
                items.forEach(item => {
                    sectionHtml += `
                        <a href="${item.url}" class="header-search-item">
                            <div class="icon-wrapper"><i class="${item.icon}"></i></div>
                            <div class="item-content">
                                <span class="item-title">${item.title}</span>
                                ${item.subtitle || item.category ? `<span class="item-meta">${item.subtitle || item.category}</span>` : ''}
                            </div>
                            ${item.badge ? `<span class="badge-status">${item.badge}</span>` : ''}
                        </a>`;
                });
                sectionHtml += `</div>`;
                return sectionHtml;
            };

            if (customHeader && data.menus) {
                html += renderSection(customHeader, data.menus, 'fa fa-history');
            } else {
                html += renderSection("{{ __('Menus') }}", data.menus, 'fa fa-th-large');
                html += renderSection("{{ __('Bookings') }}", data.bookings, 'fa fa-calendar-check');
                html += renderSection("{{ __('Packages') }}", data.trips, 'fa fa-suitcase');
                html += renderSection("{{ __('Subscribers') }}", data.users, 'fa fa-users');
            }

            if (!hasAny && searchInput.value.trim()) {
                html = `<div class="p-4 text-center text-muted"> <i class="fa fa-search-minus fa-2x mb-2 opacity-25"></i> <p class="small mb-0">{{ __('No matching results found') }}</p> </div>`;
            }

            resultsDropdown.innerHTML = html;
            resultsDropdown.classList.toggle('show', html.length > 0);
        }

        function highlightItem(items) {
            items.forEach(i => i.classList.remove('active'));
            if (selectedIndex > -1) {
                items[selectedIndex].classList.add('active');
                items[selectedIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        function recordRecentPage() {
            const pageTitle = document.title.split('-')[0].trim();
            const url = window.location.href;
            if (!pageTitle || pageTitle === 'Dashboard') return;
            const currentMenu = window.adminMenus?.find(m => m.url === url) || { title: pageTitle, url: url, icon: 'fas fa-link' };
            let recent = getRecentPages();
            recent = recent.filter(r => r.url !== url);
            recent.unshift(currentMenu);
            recent = recent.slice(0, 5);
            localStorage.setItem('admin_recent_pages', JSON.stringify(recent));
        }

        function getRecentPages() {
            try { return JSON.parse(localStorage.getItem('admin_recent_pages')) || []; } catch(e) { return []; }
        }

        setTimeout(recordRecentPage, 1000);
    });
</script>
{{-- Header end --}}
