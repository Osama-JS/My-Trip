import "./bootstrap";

// Template Scripts (Depend on global jQuery and Plugins)
import "./template/settings.js";
import "./template/custom.js";
import "./template/dlabnav-init.js";
import "./template/demo.js";
import "./template/styleSwitcher.js";
import "./template/dashboard/dashboard-1.js";




/**
 * ────────────────────────────────────────────────────────────────────────────
 * ROBUST HORIZONTAL NAVIGATION LOGIC
 * Works in both LTR/RTL and handles dynamic content loading.
 * ────────────────────────────────────────────────────────────────────────────
 */
$(document).ready(function() {
    // Isolated Initialization
    if (typeof initHorizontalNav === 'function') {
        initHorizontalNav();
    }
});

function initHorizontalNav() {
    // Only run horizontal nav logic on large screens
    if (window.innerWidth <= 991) {
        // On mobile: reset any transform that may have been applied
        const menu = document.getElementById('menu');
        if (menu) menu.style.transform = '';
        return;
    }

    try {
        const navContainer = document.getElementById('nav-scroll-container');
        const menu = document.getElementById('menu');
        const prevBtn = document.getElementById('nav-prev-btn');
        const nextBtn = document.getElementById('nav-next-btn');
        
        if (!navContainer || !menu || !prevBtn || !nextBtn) {
            console.warn('Horizontal Nav elements not found');
            return;
        }

        let currentTranslate = 0;
        const step = 280; 

        function updateNavControls() {
            const containerWidth = navContainer.clientWidth;
            const menuWidth = menu.scrollWidth;
            const maxTranslate = Math.max(0, menuWidth - containerWidth);

            currentTranslate = Math.min(currentTranslate, maxTranslate);
            if (currentTranslate < 0) currentTranslate = 0;

            if (menuWidth > containerWidth) {
                if (currentTranslate < maxTranslate) nextBtn.classList.add('visible');
                else nextBtn.classList.remove('visible');
                
                if (currentTranslate > 0) prevBtn.classList.add('visible');
                else prevBtn.classList.remove('visible');
            } else {
                nextBtn.classList.remove('visible');
                prevBtn.classList.remove('visible');
            }
        }

        function scrollNav(direction) {
            const containerWidth = navContainer.clientWidth;
            const menuWidth = menu.scrollWidth;
            const maxTranslate = Math.max(0, menuWidth - containerWidth);

            if (direction === 'next') currentTranslate = Math.min(maxTranslate, currentTranslate + step);
            else currentTranslate = Math.max(0, currentTranslate - step);

            const isRtl = $('html').attr('dir') === 'rtl' || $('body').attr('direction') === 'rtl' || document.dir === 'rtl';
            const sign = isRtl ? '' : '-'; 
            menu.style.transform = `translateX(${sign}${currentTranslate}px)`;
            updateNavControls();
        }

        nextBtn.onclick = (e) => { e.preventDefault(); scrollNav('next'); };
        prevBtn.onclick = (e) => { e.preventDefault(); scrollNav('prev'); };

        navContainer.onwheel = (evt) => {
            if (menu.scrollWidth > navContainer.offsetWidth) {
                evt.preventDefault();
                scrollNav(evt.deltaY > 0 ? 'next' : 'prev');
            }
        };

        window.addEventListener('resize', () => {
            // If resized to mobile, reset transform
            if (window.innerWidth <= 991) {
                menu.style.transform = '';
                nextBtn.classList.remove('visible');
                prevBtn.classList.remove('visible');
            } else {
                updateNavControls();
            }
        });
        window.addEventListener('load', updateNavControls);
        
        // Multi-stage verification
        setTimeout(updateNavControls, 100);
        setTimeout(updateNavControls, 500);
        setTimeout(updateNavControls, 1500);
        setTimeout(updateNavControls, 3000);

        navContainer.addEventListener('mouseenter', updateNavControls);
        console.log('Horizontal Nav Initialized Successfully');
    } catch (e) {
        console.error('Horizontal Nav Error:', e);
    }
}




