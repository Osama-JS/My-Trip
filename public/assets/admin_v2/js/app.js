/**
 * Admin v2 Core Application JS (Theme, Sidebar, Live Global Search)
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Theme Management (Dark/Light Mode)
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const htmlElement = document.documentElement;
    
    // Check saved preference or system preference
    const savedTheme = localStorage.getItem('admin_v2_theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    setTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(nextTheme);
        });
    }

    function setTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);
        localStorage.setItem('admin_v2_theme', theme);
        
        if (themeToggleBtn) {
            const icon = themeToggleBtn.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fa-solid fa-sun text-warning' : 'fa-solid fa-moon';
            }
        }
    }

    // 2. Mobile Sidebar & Backdrop Toggle
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggleBtn && adminSidebar) {
        sidebarToggleBtn.addEventListener('click', function () {
            adminSidebar.classList.toggle('mobile-open');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            adminSidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // 3. Submenu Collapsible Accordion
    const menuParents = document.querySelectorAll('.menu-item.has-submenu > .menu-link');
    menuParents.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
            if (adminSidebar) {
                sessionStorage.setItem('admin_sidebar_scroll', adminSidebar.scrollTop);
            }
        });
    });

    // 4. Fullscreen Toggle
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
    }

    // 5. Global Live Search Engine & Menu Indexing
    const searchInput = document.getElementById('globalSearchInput');
    const searchDropdown = document.getElementById('globalSearchResults');
    const searchSpinner = document.getElementById('searchSpinner');
    let searchDebounceTimer;
    let activeSearchIndex = -1;

    // Index all sidebar menus for instant zero-latency client search
    initAdminMenus();
    setTimeout(recordRecentPage, 1000);

    function initAdminMenus() {
        if (window.adminMenus && window.adminMenus.length > 0) return;
        const menuItems = [];
        const links = document.querySelectorAll('.admin-sidebar .sidebar-menu a.menu-link[href]');
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;
            const titleEl = link.querySelector('span');
            const title = titleEl ? titleEl.textContent.trim() : link.textContent.trim();
            const iconEl = link.querySelector('i');
            const icon = iconEl ? iconEl.className : 'fa-solid fa-link';

            let category = '';
            const parentItem = link.closest('.menu-item.has-submenu');
            if (parentItem) {
                const parentLink = parentItem.querySelector('.menu-link > span');
                if (parentLink) category = parentLink.textContent.trim();
            } else {
                const prevCat = link.closest('.menu-item')?.previousElementSibling;
                if (prevCat && prevCat.classList.contains('menu-category')) {
                    category = prevCat.textContent.trim();
                }
            }

            menuItems.push({
                title: title,
                url: href,
                icon: icon,
                category: category,
                searchable: (category ? category + ' ' : '') + title
            });
        });
        window.adminMenus = menuItems;
    }

    if (searchInput && searchDropdown) {
        // Show recent pages on focus if input is empty
        searchInput.addEventListener('focus', function () {
            const query = this.value.trim();
            if (!query) {
                showRecentPages();
            } else {
                handleLocalSearch(query);
            }
        });

        // Instant local search + Debounced backend search
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            if (!query) {
                showRecentPages();
                return;
            }

            // 1. Instant local search (0ms delay)
            handleLocalSearch(query);

            // 2. Debounced backend search (250ms)
            clearTimeout(searchDebounceTimer);
            if (query.length >= 2) {
                searchDebounceTimer = setTimeout(function () {
                    fetchBackendSearch(query);
                }, 250);
            }
        });

        // Keyboard navigation (ArrowDown, ArrowUp, Enter, Escape)
        searchInput.addEventListener('keydown', function (e) {
            const items = searchDropdown.querySelectorAll('.search-result-item');
            if (!items.length || !searchDropdown.classList.contains('show')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeSearchIndex = (activeSearchIndex + 1) % items.length;
                updateActiveSearchItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeSearchIndex = (activeSearchIndex - 1 + items.length) % items.length;
                updateActiveSearchItem(items);
            } else if (e.key === 'Enter') {
                if (activeSearchIndex >= 0 && items[activeSearchIndex]) {
                    e.preventDefault();
                    window.location.href = items[activeSearchIndex].getAttribute('href');
                }
            } else if (e.key === 'Escape') {
                searchDropdown.classList.remove('show');
                searchInput.blur();
            }
        });

        // Dismiss on click outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });
    }

    // Ctrl + K or Cmd + K shortcut to focus search input
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (searchInput) searchInput.focus();
        }
    });

    function showRecentPages() {
        const recent = getRecentPages();
        if (recent.length > 0) {
            renderCombinedResults({
                recent: recent
            });
        } else {
            searchDropdown.classList.remove('show');
        }
    }

    function handleLocalSearch(query) {
        if (!window.adminMenus || !window.adminMenus.length) initAdminMenus();
        const filtered = (window.adminMenus || []).filter(m => m.searchable.toLowerCase().includes(query)).slice(0, 6);
        renderCombinedResults({
            menus: filtered
        });
    }

    function fetchBackendSearch(query) {
        // Resolve search endpoint intelligently: window global -> data attribute -> relative path
        let endpoint = window.ADMIN_GLOBAL_SEARCH_URL || (searchInput && searchInput.dataset.searchUrl);
        if (!endpoint) {
            const path = window.location.pathname;
            if (path.includes('/admin')) {
                const basePath = path.substring(0, path.indexOf('/admin'));
                endpoint = basePath + '/admin/global-search';
            } else {
                endpoint = '/admin/global-search';
            }
        }

        const url = endpoint + (endpoint.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query);

        if (searchSpinner) searchSpinner.classList.remove('d-none');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('HTTP ' + res.status + ' (' + res.statusText + ')');
            }
            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Expected JSON response, received ' + contentType);
            }
            return res.json();
        })
        .then(data => {
            const currentQuery = searchInput.value.trim().toLowerCase();
            const matchingMenus = (window.adminMenus || []).filter(m => m.searchable.toLowerCase().includes(currentQuery)).slice(0, 5);

            renderCombinedResults({
                menus: matchingMenus,
                bookings: data.bookings || [],
                users: data.users || [],
                trips: data.trips || []
            });
        })
        .catch(err => {
            console.warn('Backend search notice:', err.message);
            // Fallback gracefully to client menu results so UI is never broken
            const currentQuery = searchInput.value.trim().toLowerCase();
            const matchingMenus = (window.adminMenus || []).filter(m => m.searchable.toLowerCase().includes(currentQuery)).slice(0, 5);
            renderCombinedResults({
                menus: matchingMenus,
                bookings: [],
                users: [],
                trips: []
            });
        })
        .finally(() => {
            if (searchSpinner) searchSpinner.classList.add('d-none');
        });
    }

    // Expose alias for backwards compatibility
    window.fetchGlobalSearch = fetchBackendSearch;

    function renderCombinedResults(data) {
        let html = '';
        let totalCount = 0;
        activeSearchIndex = -1;
        const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

        // Helper to render section
        const renderSection = (title, items, defaultIcon, iconColorClass, badgeClass) => {
            if (!items || items.length === 0) return '';
            let sectionHtml = `<div class="search-cat-header"><i class="${defaultIcon} ${iconColorClass}"></i> ${escapeHtml(title)}</div>`;
            items.forEach(item => {
                totalCount++;
                sectionHtml += `
                    <a href="${item.url}" class="search-result-item">
                        <div class="item-icon"><i class="${item.icon || defaultIcon}"></i></div>
                        <div class="item-details">
                            <div class="item-title">${escapeHtml(item.title)}</div>
                            ${item.category || item.subtitle ? `<div class="item-subtitle">${escapeHtml(item.category || item.subtitle)}</div>` : ''}
                        </div>
                        ${item.badge ? `<span class="badge-v2 ${badgeClass || 'badge-primary'}">${escapeHtml(item.badge)}</span>` : ''}
                    </a>
                `;
            });
            return sectionHtml;
        };

        // 0. Recent pages
        if (data.recent && data.recent.length > 0) {
            html += renderSection(isRtl ? 'الصفحات الأخيرة' : 'Recent Pages', data.recent, 'fa-solid fa-clock-rotate-left', 'text-secondary', 'badge-secondary');
        }

        // 1. Navigation Menus
        if (data.menus && data.menus.length > 0) {
            html += renderSection(isRtl ? 'القوائم والشاشات' : 'Navigation Menus', data.menus, 'fa-solid fa-layer-group', 'text-primary', 'badge-primary');
        }

        // 2. Bookings
        if (data.bookings && data.bookings.length > 0) {
            html += renderSection(isRtl ? 'الحجوزات والعمليات' : 'Bookings', data.bookings, 'fa-solid fa-receipt', 'text-success', 'badge-success');
        }

        // 3. Users / Subscribers
        if (data.users && data.users.length > 0) {
            html += renderSection(isRtl ? 'المستخدمين والعملاء' : 'Users & Customers', data.users, 'fa-solid fa-users', 'text-info', 'badge-info');
        }

        // 4. Tour Packages / Trips
        if (data.trips && data.trips.length > 0) {
            html += renderSection(isRtl ? 'الرحلات والباقات' : 'Tours & Packages', data.trips, 'fa-solid fa-suitcase-rolling', 'text-warning', 'badge-warning');
        }

        if (totalCount === 0 && searchInput.value.trim()) {
            html = `
                <div style="padding: 1.5rem; text-align: center; color: var(--text-muted);">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 1.5rem; opacity: 0.4; margin-bottom: 0.5rem; display: block;"></i>
                    ${isRtl ? 'لم يتم العثور على أي نتائج مطابقة.' : 'No matching results found.'}
                </div>
            `;
        }

        searchDropdown.innerHTML = html;
        searchDropdown.classList.toggle('show', html.length > 0);
    }

    function updateActiveSearchItem(items) {
        items.forEach((item, idx) => {
            if (idx === activeSearchIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    function recordRecentPage() {
        const rawTitle = document.title.split('|')[0].split('-')[0].trim();
        const url = window.location.href;
        if (!rawTitle || rawTitle.includes('لوحة التحكم') || rawTitle.toLowerCase().includes('dashboard')) return;
        const currentMenu = (window.adminMenus || []).find(m => m.url === url) || { title: rawTitle, url: url, icon: 'fa-solid fa-link', category: '' };
        let recent = getRecentPages();
        recent = recent.filter(r => r.url !== url);
        recent.unshift(currentMenu);
        recent = recent.slice(0, 5);
        try { localStorage.setItem('admin_recent_pages', JSON.stringify(recent)); } catch (e) {}
    }

    function getRecentPages() {
        try { return JSON.parse(localStorage.getItem('admin_recent_pages')) || []; } catch (e) { return []; }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    // 6. User Profile Dropdown Toggle Handler (Resilient & Robust)
    const userDropdownWrapper = document.querySelector('.admin-header .dropdown');
    const userDropdownTrigger = document.querySelector('.user-dropdown');
    if (userDropdownTrigger && userDropdownWrapper) {
        const userMenu = userDropdownWrapper.querySelector('.dropdown-menu');
        
        userDropdownTrigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (userMenu) {
                const isOpen = userMenu.classList.contains('show');
                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                    if (m !== userMenu) m.classList.remove('show');
                });
                if (!isOpen) {
                    userMenu.classList.add('show');
                    userDropdownTrigger.setAttribute('aria-expanded', 'true');
                } else {
                    userMenu.classList.remove('show');
                    userDropdownTrigger.setAttribute('aria-expanded', 'false');
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (userMenu && userMenu.classList.contains('show') && !userDropdownWrapper.contains(e.target)) {
                userMenu.classList.remove('show');
                userDropdownTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 7. Smart Sidebar Active Tracking & Scroll Restoration
    if (adminSidebar) {
        const currentPath = window.location.pathname.replace(/\/$/, '');
        const sidebarLinks = adminSidebar.querySelectorAll('.sidebar-menu a.menu-link[href]');
        let bestMatch = null;
        let bestMatchLen = 0;

        sidebarLinks.forEach(function (link) {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;
            try {
                const linkUrl = new URL(href, window.location.origin);
                const linkPath = linkUrl.pathname.replace(/\/$/, '');
                if (currentPath === linkPath) {
                    bestMatch = link;
                    bestMatchLen = 9999;
                } else if (linkPath !== '/admin' && linkPath !== '/admin/dashboard' && currentPath.startsWith(linkPath)) {
                    if (linkPath.length > bestMatchLen) {
                        bestMatch = link;
                        bestMatchLen = linkPath.length;
                    }
                }
            } catch (err) {}
        });

        if (bestMatch) {
            bestMatch.classList.add('active');
            const parentLi = bestMatch.closest('li');
            if (parentLi) parentLi.classList.add('active');

            const parentSubmenu = bestMatch.closest('.submenu');
            if (parentSubmenu) {
                const parentMenuItem = parentSubmenu.closest('.menu-item.has-submenu');
                if (parentMenuItem) {
                    parentMenuItem.classList.add('open', 'active');
                }
            }
        }

        // Restore scroll position or scroll active item into view
        const activeItem = adminSidebar.querySelector('.submenu .menu-link.active') ||
                           adminSidebar.querySelector('.menu-item:not(.has-submenu).active') ||
                           adminSidebar.querySelector('.menu-item.active');

        const savedScroll = sessionStorage.getItem('admin_sidebar_scroll');
        if (savedScroll !== null && savedScroll !== '0') {
            adminSidebar.scrollTop = parseInt(savedScroll, 10);
        } else if (activeItem) {
            setTimeout(function () {
                activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }, 80);
        }

        adminSidebar.addEventListener('scroll', function () {
            sessionStorage.setItem('admin_sidebar_scroll', adminSidebar.scrollTop);
        }, { passive: true });
    }
});
