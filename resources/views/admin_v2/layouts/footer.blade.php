<footer style="padding: 1.25rem 2rem; border-top: 1px solid var(--border-color); background: var(--bg-card); display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted); margin-top: auto;">
    <div>
        &copy; {{ date('Y') }} <strong>{{ config('app.name', 'My-Trip') }}</strong>. {{ app()->getLocale() == 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}
    </div>
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span>{{ app()->getLocale() == 'ar' ? 'إصدار اللوحة:' : 'Version:' }} <strong style="color: var(--primary);">v2.0 Modern</strong></span>
    </div>
</footer>
