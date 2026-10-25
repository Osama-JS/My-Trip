/**
 * Admin v2 Unified Notifications & Alerts Engine
 */
const Notify = {
    // Helper to get unified Toastr options respecting layout and direction
    _getToastrOptions: function (duration = 4500) {
        const isRtl = document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.lang === 'ar';
        return {
            closeButton: true,
            progressBar: true,
            positionClass: isRtl ? 'toast-top-left' : 'toast-top-right',
            timeOut: duration.toString(),
            extendedTimeOut: '1500',
            rtl: isRtl,
            showDuration: '250',
            hideDuration: '250',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
    },

    // 1. Toastr Notifications
    success: function (message, title = 'نجاح') {
        if (typeof toastr !== 'undefined') {
            toastr.options = this._getToastrOptions(4000);
            toastr.success(message, title);
        } else {
            alert(message);
        }
    },

    error: function (message, title = 'خطأ') {
        if (typeof toastr !== 'undefined') {
            toastr.options = this._getToastrOptions(5500);
            toastr.error(message, title);
        } else {
            alert(message);
        }
    },

    info: function (message, title = 'تنبيه') {
        if (typeof toastr !== 'undefined') {
            toastr.options = this._getToastrOptions(4500);
            toastr.info(message, title);
        }
    },

    warning: function (message, title = 'تحذير') {
        if (typeof toastr !== 'undefined') {
            toastr.options = this._getToastrOptions(5000);
            toastr.warning(message, title);
        }
    },

    // 2. SweetAlert2 Confirmation Dialog
    confirm: function (options) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: options.title || 'هل أنت متأكد؟',
                text: options.text || 'لن تتمكن من التراجع عن هذه العملية!',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                confirmButtonText: options.confirmButtonText || 'نعم، استمر',
                cancelButtonText: options.cancelButtonText || 'إلغاء',
                reverseButtons: true
            });
        } else {
            return Promise.resolve({ isConfirmed: confirm(options.text || 'هل أنت متأكد؟') });
        }
    }
};

// Auto handle delete buttons with data-confirm
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-confirm-delete, [data-confirm]').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const message = this.getAttribute('data-confirm') || 'هل أنت متأكد من رغبتك في حذف هذا العنصر نهائياً؟';

            Notify.confirm({
                title: 'تأكيد الحذف',
                text: message,
                icon: 'warning',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then(function (result) {
                if (result.isConfirmed) {
                    if (form) form.submit();
                    else if (button.getAttribute('href')) window.location.href = button.getAttribute('href');
                }
            });
        });
    });
});
