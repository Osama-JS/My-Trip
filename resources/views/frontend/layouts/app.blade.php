@php $locale = app()->getLocale(); @endphp
@php
    $footerLogo = \App\Models\Setting::get('site_logo');
    $siteName = \App\Models\Setting::get('site_name_' . app()->getLocale(), config('app.name'));
    $siteDescription = \App\Models\Setting::get('site_description_' . app()->getLocale(), config('app.name'));
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Tourism Platform')) - {{ $siteName }}</title>
    <meta name="description" content="@yield('meta_description', __('Discover amazing travel experiences. Book tours, flights, and hotels with the best prices.'))">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Premium Frontend CSS --}}
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ filemtime(public_path('css/frontend.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>
<body class="frontend-body">

    {{-- ═══ HEADER ═══ --}}
    <header class="fe-header" id="feHeader">
        <div class="fe-container fe-header-inner">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="fe-logo">
                <div class="fe-logo-icon">
                    @if($footerLogo)
                        <img src="{{ asset($footerLogo) }}" alt="{{ $siteName }}" style="height: 32px; width: auto; object-fit: contain;">
                    @else
                        <i class="fas fa-plane"></i>
                    @endif
                </div>
                <span>{{ $siteName }}</span>
            </a>

            {{-- Navigation (Becomes Mobile Drawer) --}}
            <div class="fe-nav-overlay" id="feNavOverlay" onclick="document.getElementById('feNav').classList.remove('open'); document.getElementById('feNavOverlay').classList.remove('active'); document.body.style.overflow='';"></div>
            <nav class="fe-nav" id="feNav">
                <div class="fe-nav-header-mobile">
                    <div class="fe-logo">
                        <div class="fe-logo-icon">
                            @if($footerLogo)
                                <img src="{{ asset($footerLogo) }}" alt="{{ $siteName }}" style="height: 32px; width: auto; object-fit: contain;">
                            @else
                                <i class="fas fa-plane"></i>
                            @endif
                        </div>
                        <span>{{ $siteName }}</span>
                    </div>
                    <button class="fe-nav-close" onclick="document.getElementById('feNav').classList.remove('open'); document.getElementById('feNavOverlay').classList.remove('active'); document.body.style.overflow='';">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="fe-nav-links">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> {{ __('Home') }}
                    </a>
                    <a href="{{ route('trips.index') }}" class="{{ request()->routeIs('trips.*') ? 'active' : '' }}">
                        <i class="fas fa-suitcase-rolling"></i> {{ __('Tour Packages') }}
                    </a>
                    <a href="{{ route('flights') }}" class="{{ request()->routeIs('flights') ? 'active' : '' }}">
                        <i class="fas fa-plane-departure"></i> {{ __('Flights') }}
                    </a>
                    <a href="{{ route('hotels') }}" class="{{ request()->routeIs('hotels') ? 'active' : '' }}">
                        <i class="fas fa-hotel"></i> {{ __('Hotels') }}
                    </a>
                    <a href="{{ route('destinations') }}" class="{{ request()->routeIs('destinations') ? 'active' : '' }}">
                        <i class="fas fa-map-marked-alt"></i> {{ __('Destinations') }}
                    </a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        <i class="fas fa-info-circle"></i> {{ __('About Us') }}
                    </a>
                </div>

                {{-- Mobile Auth & Lang inside Drawer --}}
                <div class="fe-nav-bottom-mobile">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard') }}" class="fe-btn fe-btn-primary w-full" style="justify-content: center;">
                            <i class="fas fa-user"></i> {{ __('Dashboard') }}
                        </a>
                    @else
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <a href="{{ route('login') }}" class="fe-btn fe-btn-outline" style="justify-content: center; padding: 10px;">
                                {{ __('Login') }}
                            </a>
                            <a href="{{ route('register') }}" class="fe-btn fe-btn-primary" style="justify-content: center; padding: 10px;">
                                {{ __('Register') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </nav>

            {{-- Actions (Desktop only for Auth, Mobile for Toggle and Lang) --}}
            <div class="fe-header-actions">
                {{-- Language Switch (Global) --}}
                @if($locale == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}" class="fe-lang-switch">
                        <i class="fas fa-globe"></i> EN
                    </a>
                @else
                    <a href="{{ route('lang.switch', 'ar') }}" class="fe-lang-switch">
                        <i class="fas fa-globe"></i> عربي
                    </a>
                @endif

                <div class="fe-desktop-actions">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard') }}" class="fe-btn fe-btn-primary fe-btn-sm">
                            <i class="fas fa-user"></i> {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="fe-btn fe-btn-outline fe-btn-sm">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ route('register') }}" class="fe-btn fe-btn-primary fe-btn-sm">
                            {{ __('Register') }}
                        </a>
                    @endauth
                </div>

                {{-- Mobile menu Toggle --}}
                <button class="fe-menu-toggle" onclick="document.getElementById('feNav').classList.add('open'); document.getElementById('feNavOverlay').classList.add('active'); document.body.style.overflow='hidden';">
                    <i class="fas fa-bars" style="font-size:1.2rem"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main>
        @if(session('success'))
            <div style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:9999;background:var(--success);color:white;padding:12px 24px;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.15);font-weight:600;animation:fadeInUp 0.4s ease">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    {{-- ═══ FOOTER ═══ --}}
     @php
        $facebookUrl = \App\Models\Setting::get('facebook_url');
        $twitterUrl = \App\Models\Setting::get('twitter_url' );
        $instagramUrl = \App\Models\Setting::get('instagram_url');
    @endphp
    <footer class="fe-footer">
        <div class="fe-container">
            <div class="fe-footer-grid">
                {{-- Brand --}}
                <div class="fe-footer-brand">
                    <a href="{{ route('home') }}" class="fe-logo">
                        <div class="fe-logo-icon">
                            @if($footerLogo)
                                <img src="{{ asset($footerLogo) }}" alt="{{ $siteName }}" style="height: 32px; width: auto; object-fit: contain;">
                            @else
                                <i class="fas fa-plane"></i>
                            @endif
                        </div>
                        <span>{{ $siteName }}</span>
                    </a>
                    <p>{{ $siteDescription }}</p>
                    <div class="fe-footer-social">
                        <a href="{{ $facebookUrl }}"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ $twitterUrl }}"><i class="fab fa-twitter"></i></a>
                        <a href="{{ $instagramUrl }}"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="fe-footer-heading">{{ __('Quick Links') }}</h4>
                    <ul class="fe-footer-links">
                        <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('trips.index') }}">{{ __('Tour Packages') }}</a></li>
                        <li><a href="{{ route('flights') }}">{{ __('Flights') }}</a></li>
                        <li><a href="{{ route('hotels') }}">{{ __('Hotels') }}</a></li>
                        <li><a href="{{ route('destinations') }}">{{ __('Destinations') }}</a></li>
                    </ul>
                </div>

                {{-- Services --}}
                <div>
                    <h4 class="fe-footer-heading">{{ __('Services') }}</h4>
                    <ul class="fe-footer-links">
                        <li><a href="{{ route('trips.index') }}">{{ __('Tour Packages') }}</a></li>
                        <li><a href="{{ route('flights') }}">{{ __('Flight Booking') }}</a></li>
                        <li><a href="{{ route('hotels') }}">{{ __('Hotel Booking') }}</a></li>
                        <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                  @php
                    $contactPhone = \App\Models\Setting::get('contact_phone');
                    $contactEmail = \App\Models\Setting::get('contact_email');
                @endphp
                <div>
                    <h4 class="fe-footer-heading">{{ __('Contact Us') }}</h4>
                    <ul class="fe-footer-links">
                        <li><i class="fas fa-envelope" style="margin-inline-end:8px;color:var(--accent)"></i> {{$contactEmail}}</li>
                        <li><i class="fas fa-phone" style="margin-inline-end:8px;color:var(--accent)"></i> {{$contactPhone}}</li>
                        <li><i class="fas fa-map-marker-alt" style="margin-inline-end:8px;color:var(--accent)"></i> {{ __('Saudi Arabia') }}</li>
                    </ul>
                </div>
            </div>

            <div class="fe-footer-bottom">
                <p>© {{ date('Y') }} {{ $siteName }} - {{ __('All Rights Reserved') }}</p>
            </div>
        </div>
    </footer>

    {{-- ═══ SCRIPTS ═══ --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('feHeader');
            if(header) header.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Favorite Toggle (Global)
        function toggleFavorite(btn) {
            const tripId = btn.dataset.tripId;
            const icon = btn.querySelector('i');
            
            btn.disabled = true;
            
            fetch(`{{ url('customer/favorites') }}/${tripId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    btn.classList.add('active');
                    if(icon) icon.className = 'fas fa-heart';
                    // Optional: toaster notification
                } else {
                    btn.classList.remove('active');
                    if(icon) icon.className = 'far fa-heart';
                }
            })
            .catch(err => console.error('Favorite toggle failed:', err))
            .finally(() => {
                btn.disabled = false;
            });
        }

        // Scroll animations
        const animateElements = document.querySelectorAll('.fe-animate');
// ... rest of the script
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        animateElements.forEach(el => observer.observe(el));

        // Banner slider
        function initSlider() {
            const track = document.querySelector('.fe-banner-track');
            const dots = document.querySelectorAll('.fe-banner-dot');
            if (!track || !dots.length) return;
            let current = 0;
            const total = dots.length;

            function goTo(i) {
                current = i;
                const dir = document.documentElement.dir === 'rtl' ? 1 : -1;
                track.style.transform = `translateX(${dir * current * 100}%)`;
                dots.forEach((d, idx) => d.classList.toggle('active', idx === current));
            }

            dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));
            setInterval(() => goTo((current + 1) % total), 5000);
        }
        document.addEventListener('DOMContentLoaded', initSlider);
    </script>

    @stack('scripts')
</body>
</html>
