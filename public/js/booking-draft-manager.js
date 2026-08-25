/**
 * BookingDraftManager
 * Automatically saves and restores form inputs (passenger info, contact info, notes, selections)
 * in sessionStorage so unauthenticated guests never lose their inputs when logging in or registering.
 */
class BookingDraftManager {
    constructor(formSelector, storageKey) {
        this.form = typeof formSelector === 'string' ? document.querySelector(formSelector) : formSelector;
        this.storageKey = 'draft_' + storageKey;
        if (!this.form) return;
        this.init();
    }

    init() {
        this.restoreDraft();
        this.bindEvents();
    }

    bindEvents() {
        // Auto-save on input or change (debounced)
        let timer = null;
        this.form.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => this.saveDraft(), 300);
        });
        this.form.addEventListener('change', () => {
            this.saveDraft();
        });

        // When clicking login buttons with save-draft attribute or class
        document.querySelectorAll('.btn-save-draft-and-login, .fe-btn-guest-login, .fe-btn-confirm').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.saveDraft();
            });
        });

        // Clear draft when the form is submitted
        this.form.addEventListener('submit', () => {
            // Keep draft momentarily in case of validation error, but clear on unload if proceeding
            window.addEventListener('pagehide', () => this.clearDraft(), { once: true });
        });
    }

    saveDraft() {
        if (!this.form) return;
        const formData = {};
        const elements = this.form.elements;

        for (let i = 0; i < elements.length; i++) {
            const el = elements[i];
            if (!el.name || el.type === 'password' || el.type === 'file' || el.name === '_token') continue;

            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.checked) {
                    formData[el.name] = el.value;
                }
            } else {
                if (el.value !== '') {
                    formData[el.name] = el.value;
                }
            }
        }

        try {
            sessionStorage.setItem(this.storageKey, JSON.stringify(formData));
        } catch (e) {
            console.warn('Draft save error:', e);
        }
    }

    restoreDraft() {
        try {
            const saved = sessionStorage.getItem(this.storageKey);
            if (!saved) return;
            const data = JSON.parse(saved);
            let restoredCount = 0;

            for (const [name, val] of Object.entries(data)) {
                const el = this.form.elements[name];
                if (el) {
                    if (el instanceof RadioNodeList || el.type === 'radio') {
                        const targetRadio = this.form.querySelector(`input[name="${name}"][value="${val}"]`);
                        if (targetRadio) {
                            targetRadio.checked = true;
                            targetRadio.dispatchEvent(new Event('change', { bubbles: true }));
                            restoredCount++;
                        }
                    } else if (el.type === 'checkbox') {
                        el.checked = (el.value === val || val === true || val === '1');
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                        restoredCount++;
                    } else {
                        // Only set if field is currently empty
                        if (!el.value || el.value === '') {
                            el.value = val;
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                            restoredCount++;
                        }
                    }
                }
            }

            if (restoredCount > 0) {
                this.showRestoredToast();
            }
        } catch (e) {
            console.warn('Draft restore error:', e);
        }
    }

    showRestoredToast() {
        if (document.querySelector('.fe-draft-restored-toast')) return;
        const toast = document.createElement('div');
        toast.className = 'fe-draft-restored-toast';
        const isAr = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
        toast.innerHTML = `
            <div class="toast-inner">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.2rem; flex-shrink:0;"></i>
                <div style="flex:1;">
                    <strong style="display:block; font-size:0.92rem; color:#0f172a; margin-bottom:2px;">${isAr ? 'تم استرجاع البيانات المحفوظة' : 'Details Restored'}</strong>
                    <span style="font-size:0.84rem; color:#475569;">${isAr ? 'تم استرجاع كافة بيانات الحجز والمسافرين تلقائياً لتتابع الحجز دون إعادة الكتابة.' : 'Your passenger and booking details were automatically restored so you can proceed immediately.'}</span>
                </div>
                <button type="button" style="background:none; border:none; color:#94a3b8; font-size:1.4rem; cursor:pointer; padding:0 4px; line-height:1;" onclick="this.closest('.fe-draft-restored-toast').remove()">&times;</button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 6000);
    }

    clearDraft() {
        try {
            sessionStorage.removeItem(this.storageKey);
        } catch (e) {}
    }
}

// Attach global helper to save draft before navigating
window.saveDraftAndRedirect = function(formSelector, storageKey, redirectUrl) {
    const manager = new BookingDraftManager(formSelector, storageKey);
    manager.saveDraft();
    window.location.href = redirectUrl;
};
