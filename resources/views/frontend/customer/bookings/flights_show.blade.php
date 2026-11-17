@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Flight Booking') . ' · #' . $booking->booking_reference)
@section('page-title', __('Flight Booking Details'))

@push('styles')
<style>
/* ─── Variables & Animations ─── */
:root {
    --detail-border-radius: 20px;
    --ticket-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -4px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0, 0, 0, 0.025);
    --ticket-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(37, 99, 235, 0.15);
    --fd-success: #10b981;
    --fd-danger: #ef4444;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.flight-details-container {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* ─── Back Link ─── */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    text-decoration: none !important;
    font-size: .88rem;
    margin-bottom: 24px;
    font-weight: 700;
    transition: all 0.2s ease;
}
.back-link:hover {
    color: var(--primary-blue);
    transform: translateX(-4px);
}
[dir="rtl"] .back-link:hover {
    transform: translateX(4px);
}

/* ─── Boarding Pass ─── */
.boarding-pass {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    overflow: hidden;
    box-shadow: var(--ticket-shadow);
    margin-bottom: 28px;
    position: relative;
}
.pass-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 16px 24px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pass-title {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,0.7);
    text-transform: uppercase;
}
.pass-pnr {
    font-size: 0.9rem;
    font-weight: 900;
    letter-spacing: 0.5px;
    background: rgba(255,255,255,0.12);
    padding: 4px 14px;
    border-radius: 8px;
    color: #fff;
}
.pass-body {
    padding: 28px;
    position: relative;
}
.pass-airport-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.pass-airport-code {
    display: flex;
    flex-direction: column;
}
.pass-airport-code h3 {
    font-size: 2.5rem;
    font-weight: 950;
    color: var(--primary-blue);
    margin: 0;
    line-height: 1;
    letter-spacing: -0.5px;
}
.pass-airport-code span {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-top: 4px;
}
.pass-airport-code.dest-code {
    align-items: flex-end;
}
.pass-path-line {
    flex: 1;
    height: 2px;
    background: var(--border-color);
    margin: 0 24px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pass-path-line i {
    font-size: 1.25rem;
    color: var(--primary-blue);
    background: var(--bg-card);
    padding: 0 12px;
    z-index: 2;
    transition: background-color 0.3s, transform 0.2s;
}
[dir="rtl"] .pass-path-line i,
html[dir="rtl"] .pass-path-line i {
    transform: scaleX(-1);
}

.boarding-pass-stub-line {
    position: relative;
    border-top: 2px dashed var(--border-color);
    margin-top: 20px;
    padding-top: 20px;
}
.boarding-pass-stub-line::before, .boarding-pass-stub-line::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    top: -11px;
    z-index: 5;
    transition: background-color 0.3s, border-color 0.3s;
}
.boarding-pass-stub-line::before {
    left: -39px;
}
.boarding-pass-stub-line::after {
    right: -39px;
}
[dir="rtl"] .boarding-pass-stub-line::before {
    left: auto;
    right: -39px;
}
[dir="rtl"] .boarding-pass-stub-line::after {
    right: auto;
    left: -39px;
}

.pass-details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 768px) {
    .pass-details-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}
.pass-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.pass-val {
    display: block;
    font-size: 0.95rem;
    font-weight: 850;
    color: var(--text-main);
}

/* ─── Detail Grid ─── */
.booking-detail-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}
@media (max-width: 991px) {
    .booking-detail-grid {
        grid-template-columns: 1fr;
    }
}

/* ─── Detail Cards ─── */
.detail-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    box-shadow: var(--ticket-shadow);
    margin-bottom: 24px;
    overflow: hidden;
}
.detail-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    font-weight: 850;
    font-size: 1.05rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 0, 0, 0.005);
}
.detail-card-header h5 {
    margin: 0;
    font-weight: 850;
    font-size: 1.05rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 10px;
}
.detail-card-header h5 i {
    color: var(--primary-blue);
    font-size: 1.15rem;
}
.detail-card-body {
    padding: 24px;
}

/* ─── Callout Alerts ─── */
.callout-alert {
    padding: 16px 20px;
    border-radius: 14px;
    margin-bottom: 24px;
    display: flex;
    align-items: start;
    gap: 14px;
    border: 1px solid transparent;
}
.callout-success {
    background: rgba(16, 185, 129, 0.06);
    color: #15803d;
    border-color: rgba(16, 185, 129, 0.15);
}
body.dark-mode .callout-success {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
}
.callout-warning {
    background: rgba(249, 115, 22, 0.06);
    color: #b45309;
    border-color: rgba(249, 115, 22, 0.15);
}
body.dark-mode .callout-warning {
    background: rgba(249, 115, 22, 0.12);
    color: #f97316;
}
.callout-danger {
    background: rgba(239, 68, 68, 0.06);
    color: #b91c1c;
    border-color: rgba(239, 68, 68, 0.15);
}
body.dark-mode .callout-danger {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}
.callout-alert i {
    font-size: 1.25rem;
    margin-top: 2px;
}

/* ─── Passengers Seating Tags ─── */
.passenger-item {
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
}
.passenger-item:hover {
    border-color: rgba(37, 99, 235, 0.15);
    background: rgba(37, 99, 235, 0.01);
}
.passenger-item:last-child {
    margin-bottom: 0;
}
.passenger-main {
    display: flex;
    align-items: center;
    gap: 16px;
}
.passenger-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: .95rem;
    flex-shrink: 0;
    box-shadow: 0 4px 8px rgba(37, 99, 235, 0.15);
}
.passenger-info .p-name {
    font-weight: 800;
    font-size: 0.95rem;
    color: var(--text-main);
}
.passenger-info .p-meta {
    font-size: .8rem;
    color: var(--text-muted);
    margin-top: 3px;
}
.passenger-docs {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
}
.ticket-tag, .passport-tag {
    display: flex;
    flex-direction: column;
    font-size: 0.72rem;
    background: rgba(0, 0, 0, 0.02);
    border: 1px solid var(--border-color);
    padding: 4px 10px;
    border-radius: 8px;
}
body.dark-mode .ticket-tag, body.dark-mode .passport-tag {
    background: rgba(255, 255, 255, 0.02);
}
.ticket-tag label, .passport-tag label {
    font-size: 0.6rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.ticket-tag strong, .passport-tag strong {
    color: var(--text-main);
    font-weight: 800;
}

/* ─── Detailed Journey Timeline ─── */
.segment-timeline {
    padding: 24px;
}
.segment-step {
    position: relative;
    padding-bottom: 30px;
    padding-inline-start: 32px;
}
.segment-step:last-child {
    padding-bottom: 0;
}
.segment-step::before {
    content: '';
    position: absolute;
    inset-inline-start: 11px;
    top: 24px;
    width: 2px;
    height: calc(100% - 16px);
    background: var(--border-color);
}
.segment-step:last-child::before {
    display: none;
}
.seg-dot {
    position: absolute;
    inset-inline-start: 0;
    top: 2px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--bg-card);
    border: 2px solid var(--primary-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-blue);
    font-size: 0.7rem;
    z-index: 2;
}
.seg-airline {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.seg-airline img {
    height: 24px;
    width: auto;
    border-radius: 4px;
    background: #fff;
    padding: 2px;
    border: 1px solid var(--border-color);
}
.seg-airline span {
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--text-main);
}
.seg-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-main);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 16px 20px;
    gap: 16px;
}
@media (max-width: 576px) {
    .seg-main {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    .seg-point.text-end {
        text-align: center !important;
    }
}
.seg-point .time {
    font-size: 1.2rem;
    font-weight: 900;
    color: var(--text-main);
}
.seg-point .airport {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--primary-blue);
    margin: 2px 0;
}
.seg-point .date {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
}
.seg-path {
    flex: 1;
    position: relative;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.seg-path-line {
    width: 100%;
    height: 2px;
    border-top: 2px dashed var(--border-color);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
}
.seg-path i {
    position: relative;
    z-index: 2;
    background: var(--bg-main);
    padding: 0 10px;
    color: var(--primary-blue);
    font-size: 0.95rem;
    transition: transform 0.2s;
}
[dir="rtl"] .seg-path i,
html[dir="rtl"] .seg-path i {
    transform: scaleX(-1);
}
.seg-path .path-dur {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 700;
    margin-top: 4px;
    position: relative;
    z-index: 2;
    background: var(--bg-main);
    padding: 0 6px;
}
.seg-footer {
    margin-top: 10px;
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    gap: 16px;
}
.seg-footer span {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ─── Sidebar Pricing Card ─── */
.summary-total {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 18px;
    padding: 28px 24px;
    color: #fff;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    position: relative;
    overflow: hidden;
}
.summary-total::after {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
    top: -30px;
    right: -30px;
}
.summary-total .amount {
    font-size: 2.2rem;
    font-weight: 950;
    letter-spacing: -0.5px;
    line-height: 1.1;
}
.summary-total .amount span {
    font-size: 1.1rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.7);
    margin-inline-start: 4px;
}
.summary-total .amount-label {
    font-size: .85rem;
    color: #94a3b8;
    margin-top: 6px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.price-breakdown {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 16px;
    margin-top: 16px;
    text-align: start;
}
.row-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}
.row-info:last-child {
    margin-bottom: 0;
}
.row-info span {
    color: rgba(255, 255, 255, 0.6);
}
.row-info strong {
    color: #fff;
    font-weight: 700;
}

/* ─── Actions Sidebar ─── */
.actions-stack {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    font-size: .88rem;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
}
.action-btn-primary {
    background: var(--primary-blue);
    color: #fff;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
}
.action-btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    color: #fff;
}
.action-btn-success {
    background: var(--fd-success, #10b981);
    color: #fff;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
}
.action-btn-success:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
    color: #fff;
}
.action-btn-outline {
    border-color: var(--border-color);
    background: var(--bg-card);
    color: var(--text-main);
}
.action-btn-outline:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue);
    background: rgba(37, 99, 235, 0.03);
    transform: translateY(-2px);
}
.action-btn-danger-soft {
    background: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.15);
}
.action-btn-danger-soft:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-2px);
}
.support-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 10px;
    border-top: 1px dashed var(--border-color);
}
.support-links a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}
.support-links a:hover {
    color: var(--primary-blue);
}

/* ─── Ultra Modern Cancel Modal ─── */
.cancel-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(8px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}
.cancel-modal-container {
    background: #ffffff;
    border-radius: 24px;
    width: 100%;
    max-width: 540px;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    overflow: hidden;
    animation: slideUpModal 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(226, 232, 240, 0.8);
    position: relative;
    transition: all 0.3s ease;
}
@keyframes slideUpModal {
    from { transform: translateY(24px) scale(0.97); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
.modal-top-accent {
    height: 5px;
    width: 100%;
    background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
    transition: background 0.3s ease;
}
.cancel-modal-container.theme-danger .modal-top-accent {
    background: linear-gradient(90deg, #ef4444 0%, #b91c1c 100%);
}
.cancel-modal-container.theme-success .modal-top-accent {
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
}
.cancel-modal-container.theme-info .modal-top-accent {
    background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
}
.cancel-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
}
.btn-close-modal {
    background: #f1f5f9;
    border: none;
    font-size: 1.25rem;
    color: #64748b;
    cursor: pointer;
    line-height: 1;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-close-modal:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: rotate(90deg);
}
.cancel-modal-body {
    padding: 24px;
    max-height: 72vh;
    overflow-y: auto;
}
.cancel-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 18px 24px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}
.btn-modal-cancel {
    padding: 11px 22px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-weight: 700;
    font-size: 0.92rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-modal-cancel:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.btn-modal-confirm {
    padding: 11px 24px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.92rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    transition: all 0.2s;
}
.btn-modal-confirm:hover:not(:disabled) {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
}
.btn-modal-confirm:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}
.modal-icon-badge {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: rgba(239, 68, 68, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    transition: all 0.3s ease;
}
.cancel-modal-container.theme-danger .modal-icon-badge {
    background: rgba(239, 68, 68, 0.12);
    color: #dc2626;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
}
.cancel-modal-container.theme-success .modal-icon-badge {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.08);
}
.cancel-modal-container.theme-info .modal-icon-badge {
    background: rgba(37, 99, 235, 0.12);
    color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
}
.btn-modal-support {
    padding: 11px 22px;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff !important;
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    transition: all 0.2s;
}
.btn-modal-support:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
}

/* Danger / Warning Card Elements inside modal */
.danger-hero-card {
    background: linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%);
    border: 1px solid #fecaca;
    border-inline-start: 5px solid #dc2626;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 20px;
    position: relative;
}
.danger-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 50px;
    background: #b91c1c;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(185, 28, 28, 0.25);
    margin-bottom: 12px;
}
.success-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 50px;
    background: #059669;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
    margin-bottom: 12px;
}
.info-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 50px;
    background: #2563eb;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    margin-bottom: 12px;
}
.pulse-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffffff;
    animation: dangerPulse 1.5s infinite;
}
@keyframes dangerPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.3); }
}
.price-breakdown-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 18px;
}
.price-breakdown-box.border-danger-soft {
    border-color: #fecaca;
    background: #fafaf9;
}
.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 0.92rem;
}
.price-row.total-row {
    border-top: 1px dashed #e2e8f0;
    padding-top: 12px;
    margin-top: 6px;
    font-size: 1.05rem;
    font-weight: 800;
}
.protection-callout {
    background: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: 14px;
    padding: 14px 16px;
    color: #991b1b;
    font-size: 0.88rem;
    line-height: 1.6;
}

/* ─── Timeline Card ─── */
.timeline-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--detail-border-radius);
    padding: 24px;
    box-shadow: var(--ticket-shadow);
    margin-bottom: 24px;
}
.timeline-v {
    display: flex;
    flex-direction: column;
}
.tl-item {
    display: flex;
    gap: 14px;
    position: relative;
    padding-bottom: 24px;
}
.tl-item:last-child {
    padding-bottom: 0;
}
.tl-item::before {
    content: '';
    position: absolute;
    inset-inline-start: 17px;
    top: 34px;
    width: 2px;
    height: calc(100% - 14px);
    background: var(--border-color);
}
.tl-item:last-child::before {
    display: none;
}
.tl-item.done::before {
    background: var(--fd-success, #10b981);
}
.tl-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    flex-shrink: 0;
    background: var(--bg-main);
    border: 2px solid var(--border-color);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    position: relative;
    z-index: 1;
}
.tl-item.done .tl-dot {
    background: var(--fd-success, #10b981);
    border-color: var(--fd-success, #10b981);
    color: white;
}
.tl-text strong {
    display: block;
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 2px;
}
.tl-text small {
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
}

/* Status Badge */
.status-badge-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
}
.status-badge-wrapper .pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.status-badge-wrapper.status-confirmed {
    background: rgba(16, 185, 129, 0.08);
    color: #15803d;
}
.status-badge-wrapper.status-confirmed .pulse-dot {
    background: #10b981;
}
.status-badge-wrapper.status-pending {
    background: rgba(249, 115, 22, 0.08);
    color: #c2410c;
}
.status-badge-wrapper.status-pending .pulse-dot {
    background: #f97316;
}
.status-badge-wrapper.status-cancelled {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.status-badge-wrapper.status-cancelled .pulse-dot {
    background: #ef4444;
}

/* Expiry timer */
.expired-msg-sidebar {
    background: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    padding: 12px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 750;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid rgba(239, 68, 68, 0.15);
}

/* ─── Premium Flight Itinerary Timeline & Nodes ─── */
.itin-leg-card {
    border-radius: 18px;
    padding: 22px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: all 0.3s ease;
}
.itin-leg-card:hover {
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.06);
}
.itin-leg-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 16px;
    border-bottom: 1.5px solid var(--border-color);
    margin-bottom: 20px;
}
.itin-leg-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 850;
    font-size: 1.05rem;
}
.itin-leg-badge {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.itin-leg-badge.badge-direct {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.25);
}
.itin-leg-badge.badge-transit {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
body.dark-mode .itin-leg-badge.badge-transit {
    background: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.3);
}

/* Timeline Layout */
.itin-timeline {
    position: relative;
    padding-inline-start: 6px;
}
.itin-segment-block {
    position: relative;
    margin-bottom: 12px;
}
.itin-point-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    position: relative;
    z-index: 2;
}
.itin-point-time {
    min-width: 82px;
    text-align: end;
    flex-shrink: 0;
}
.itin-time-main {
    font-size: 1.25rem;
    font-weight: 900;
    color: var(--text-main);
    line-height: 1.1;
    letter-spacing: -0.3px;
}
.itin-time-sub {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 3px;
}
.itin-point-node {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid var(--primary-blue);
    flex-shrink: 0;
    margin-top: 2px;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
}
body.dark-mode .itin-point-node {
    background: #1e293b;
}
.itin-point-node.node-dest {
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}
.itin-point-info {
    flex: 1;
    min-width: 0;
}
.itin-airport-city {
    font-weight: 850;
    font-size: 1.02rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.itin-code-pill {
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary-blue);
    font-weight: 900;
    font-size: 0.8rem;
    padding: 2px 8px;
    border-radius: 6px;
}
.itin-airport-name {
    font-size: 0.84rem;
    color: var(--text-muted);
    font-weight: 500;
    margin-top: 3px;
}

/* Flight Connector Card */
.itin-connector-wrapper {
    display: flex;
    gap: 16px;
    margin: 8px 0;
    position: relative;
}
.itin-connector-time-spacer {
    min-width: 82px;
    flex-shrink: 0;
}
.itin-connector-line-col {
    width: 22px;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    position: relative;
}
.itin-connector-line {
    width: 3px;
    background: linear-gradient(to bottom, var(--primary-blue) 0%, #10b981 100%);
    border-radius: 2px;
    min-height: 48px;
}
.itin-flight-badge-card {
    flex: 1;
    background: rgba(37, 99, 235, 0.035);
    border: 1px solid rgba(37, 99, 235, 0.15);
    border-radius: 14px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 8px;
    margin-top: 4px;
}
body.dark-mode .itin-flight-badge-card {
    background: rgba(37, 99, 235, 0.08);
    border-color: rgba(37, 99, 235, 0.22);
}
.itin-flight-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.itin-flight-airline {
    font-weight: 850;
    font-size: 0.92rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
}
.itin-flight-chip {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-muted);
    background: var(--bg-card);
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}
.itin-flight-duration {
    font-weight: 850;
    font-size: 0.85rem;
    color: var(--primary-blue);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Layover Box */
.itin-layover-wrapper {
    display: flex;
    gap: 16px;
    margin: 16px 0;
}
.itin-layover-spacer {
    min-width: 82px;
    flex-shrink: 0;
}
.itin-layover-dot-col {
    width: 22px;
    flex-shrink: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}
.itin-layover-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #f59e0b;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
}
.itin-layover-card {
    flex: 1;
    background: #fffbeb;
    border: 1.5px dashed #fcd34d;
    border-radius: 14px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
body.dark-mode .itin-layover-card {
    background: rgba(245, 158, 11, 0.1);
    border-color: rgba(245, 158, 11, 0.3);
}
.itin-layover-title {
    font-weight: 850;
    font-size: 0.92rem;
    color: #b45309;
    display: flex;
    align-items: center;
    gap: 8px;
}
body.dark-mode .itin-layover-title {
    color: #fbbf24;
}
.itin-layover-sub {
    font-size: 0.82rem;
    color: #92400e;
    font-weight: 600;
}
body.dark-mode .itin-layover-sub {
    color: #fde68a;
}

/* Cancellation Report Dark Mode & High Contrast */
body.dark-mode .cancellation-report-card {
    background: #1e293b !important;
    border-color: #7f1d1d !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
}
body.dark-mode .cancellation-metric-wallet {
    background: #064e3b !important;
    border-color: #10b981 !important;
}
body.dark-mode .cancellation-metric-wallet .metric-amount,
body.dark-mode .cancellation-metric-wallet .metric-title {
    color: #ffffff !important;
}
body.dark-mode .cancellation-metric-wallet .metric-currency {
    color: #6ee7b7 !important;
}
body.dark-mode .wallet-confirm-box {
    background: rgba(16, 185, 129, 0.12) !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}
</style>
@endpush

@section('content')
<div class="flight-details-container">

    {{-- Back Link --}}
    <a href="{{ route('customer.bookings.flights') }}" class="back-link">
        <i class="fas fa-arrow-{{ app()->isLocale('ar') ? 'right' : 'left' }}"></i>
        {{ __('Back to Bookings') }}
    </a>

    {{-- Expiry Alerts & PNR States --}}
    @php
        $flightExpiry = $booking->ticketing_time_limit ?: ($booking->created_at ? $booking->created_at->addMinutes(15) : now()->addMinutes(15));
    @endphp
    @if($booking->status === 'pending')
        <div class="callout-alert callout-warning" id="pnrTimer" data-expiry="{{ $flightExpiry->toIso8601String() }}">
            <i class="fas fa-hourglass-start fa-spin"></i>
            <div>
                <strong>{{ __('Action Required: Complete Payment') }}</strong>
                <div>{{ __('Your flight hold reservation is temporary. Please pay now to secure your ticket.') }}</div>
                <div class="timer-display-wrapper mt-2">
                    <span class="badge bg-danger p-2" id="timerDisplay" style="font-family: monospace; font-size: 1.1rem; font-weight: 800;">15:00</span>
                </div>
            </div>
        </div>
    @endif

    @php
        $pnrDisplay = $booking->pnr_code 
            ?: ($booking->supplier_confirmation_num 
            ?: ($booking->flightBooking->pnr_code ?? ($booking->flightBooking->pnr ?? null)));
        
        if (empty($pnrDisplay) && !empty($apiTripDetails)) {
            $pnrDisplay = $apiTripDetails['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['ItineraryRef']['AirReservationID'] 
                ?? $apiTripDetails['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['UniqueID'] 
                ?? null;
        }
        if (empty($pnrDisplay)) {
            $pnrDisplay = $booking->booking_reference ?: 'N/A';
        }

        // Extract legs from itinerary_data
        $itinData = $booking->flightBooking->itinerary_data ?? [];
        if (is_string($itinData)) $itinData = json_decode($itinData, true);
        $legs = [];

        // 1. Check structured segments in itinerary_data
        if (!empty($itinData['segments'])) {
            foreach ($itinData['segments'] as $legData) {
                if (isset($legData['legs']) && is_array($legData['legs'])) {
                    $legs[] = $legData['legs'];
                } elseif (isset($legData['from']) || isset($legData['DepartureAirportLocationCode'])) {
                    $legIdx = (int)($legData['leg_index'] ?? (isset($legData['is_return']) && $legData['is_return'] ? 1 : 0));
                    $legs[$legIdx][] = $legData;
                }
            }
            $legs = array_values($legs);
        } elseif (!empty($itinData) && is_array($itinData) && (isset($itinData[0]['from']) || isset($itinData[0]['DepartureAirportLocationCode']))) {
            $legsGrouped = [];
            foreach ($itinData as $s) {
                $legIdx = (int)($s['leg_index'] ?? (isset($s['is_return']) && $s['is_return'] ? 1 : 0));
                $legsGrouped[$legIdx][] = $s;
            }
            $legs = array_values($legsGrouped);
        } elseif (isset($itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'])) {
            $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'];
            if (isset($options['OriginDestinationOption']['FlightSegment'])) {
                $options = [$options['OriginDestinationOption']];
            } else {
                $options = $options['OriginDestinationOption'] ?? [];
            }
            
            foreach($options as $legIndex => $opt) {
                $segs = $opt['FlightSegment'] ?? $opt;
                $legSegments = [];
                if (isset($segs['FlightNumber'])) { 
                    $legSegments[] = $segs; 
                } else { 
                    foreach($segs as $s) { 
                        $legSegments[] = $s['FlightSegment'] ?? $s; 
                    } 
                }
                if (!empty($legSegments)) {
                    $legs[] = $legSegments;
                }
            }
        }

        // 2. Fallback to live API data if available and legs is empty
        $apiResItems = $apiTripDetails['TripDetailsResponse']['TripDetailsResult']['TravelItinerary']['ItineraryInfo']['ReservationItems'] ?? [];
        if (isset($apiResItems['ReservationItem'])) {
            $apiResItems = [$apiResItems['ReservationItem']];
        } elseif (isset($apiResItems[0]) && !isset($apiResItems[0]['ReservationItem'])) {
            $apiResItems = array_map(function($i) { return ['ReservationItem' => $i]; }, $apiResItems);
        }

        if (empty($legs) && !empty($apiResItems)) {
            $flatItems = [];
            foreach ($apiResItems as $wrapper) {
                $flatItems[] = $wrapper['ReservationItem'] ?? $wrapper;
            }
            $currentLeg = [];
            $lastArrival = null;
            $destCodeVal = $booking->flightBooking->destination ?? null;
            foreach ($flatItems as $item) {
                $itemDep = $item['DepartureAirport']['LocationCode'] ?? ($item['DepartureAirportLocationCode'] ?? '');
                $itemArr = $item['ArrivalAirport']['LocationCode'] ?? ($item['ArrivalAirportLocationCode'] ?? '');
                
                if (!empty($currentLeg)) {
                    if ($lastArrival && strtoupper($itemDep) !== strtoupper($lastArrival)) {
                        $legs[] = $currentLeg;
                        $currentLeg = [];
                    } elseif ($destCodeVal && $lastArrival && strtoupper($lastArrival) === strtoupper($destCodeVal)) {
                        $legs[] = $currentLeg;
                        $currentLeg = [];
                    }
                }
                $currentLeg[] = $item;
                $lastArrival = $itemArr;
            }
            if (!empty($currentLeg)) $legs[] = $currentLeg;
        }

        $totalLegsCount = count($legs);
        $isRoundTrip = !empty($booking->flightBooking->return_date) || $totalLegsCount > 1;

        $originCode = $booking->flightBooking->origin ?? 'N/A';
        $destCode = $booking->flightBooking->destination ?? 'N/A';

        if (!empty($apiResItems)) {
            $firstItem = $apiResItems[0]['ReservationItem'] ?? $apiResItems[0];
            $lastItem = end($apiResItems)['ReservationItem'] ?? end($apiResItems);
            
            if ($originCode === 'N/A') $originCode = $firstItem['DepartureAirport']['LocationCode'] ?? $originCode;
            if ($destCode === 'N/A') $destCode = $lastItem['ArrivalAirport']['LocationCode'] ?? $destCode;
        }

        if (($originCode === 'N/A' || $destCode === 'N/A') && !empty($legs)) {
            $firstSeg = $legs[0][0] ?? [];
            $lastSeg = end($legs[0]) ?? [];
            if ($originCode === 'N/A') $originCode = $firstSeg['DepartureAirportLocationCode'] ?? ($firstSeg['DepartureAirport']['LocationCode'] ?? ($firstSeg['from'] ?? $originCode));
            if ($destCode === 'N/A') $destCode = $lastSeg['ArrivalAirportLocationCode'] ?? ($lastSeg['ArrivalAirport']['LocationCode'] ?? ($lastSeg['to'] ?? $destCode));
        }

        $originAirport = $originCode !== 'N/A' ? \App\Models\Airport::where('airport_code', $originCode)->first() : null;
        $destAirport = $destCode !== 'N/A' ? \App\Models\Airport::where('airport_code', $destCode)->first() : null;
        
        $originName = $originAirport ? ($originAirport->city_name . ' - ' . $originAirport->airport_name) : $originCode;
        $destName = $destAirport ? ($destAirport->city_name . ' - ' . $destAirport->airport_name) : $destCode;

        $exactDepartureTime = null;
        $exactReturnTime = null;

        if (!empty($legs[0][0]['DepartureDateTime'])) {
            try { $exactDepartureTime = \Carbon\Carbon::parse($legs[0][0]['DepartureDateTime']); } catch (\Exception $e) {}
        } elseif (!empty($legs[0][0]['dep_datetime'])) {
            try { $exactDepartureTime = \Carbon\Carbon::parse($legs[0][0]['dep_datetime']); } catch (\Exception $e) {}
        }
        if (!$exactDepartureTime && $booking->flightBooking && $booking->flightBooking->departure_date) {
            $exactDepartureTime = \Carbon\Carbon::parse($booking->flightBooking->departure_date);
        }

        if ($totalLegsCount > 1 && isset($legs[1][0])) {
            $rSeg = $legs[1][0];
            $rDt = $rSeg['DepartureDateTime'] ?? ($rSeg['dep_datetime'] ?? null);
            if ($rDt) {
                try { $exactReturnTime = \Carbon\Carbon::parse($rDt); } catch (\Exception $e) {}
            }
        }
        if (!$exactReturnTime && $booking->flightBooking && $booking->flightBooking->return_date) {
            $exactReturnTime = \Carbon\Carbon::parse($booking->flightBooking->return_date);
        }
    @endphp

    {{-- ─── Boarding Pass Ticket Header ─── --}}
    <div class="boarding-pass">
        <div class="pass-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="pass-title">{{ __('BOARDING PASS / FLIGHT TICKET') }}</span>
                @php
                    $statusBadgeClass = 'bg-success';
                    $statusIcon = 'fa-check-circle';
                    $statusLabel = __('Confirmed');
                    if ($booking->status === 'pending') {
                        $statusBadgeClass = 'bg-warning text-dark';
                        $statusIcon = 'fa-hourglass-half';
                        $statusLabel = __('Pending Payment');
                    } elseif (in_array($booking->status, ['cancelled', 'failed', 'expired'])) {
                        $statusBadgeClass = 'bg-danger';
                        $statusIcon = 'fa-times-circle';
                        $statusLabel = __('Cancelled');
                    } elseif ($booking->status === 'ticketed') {
                        $statusBadgeClass = 'bg-primary';
                        $statusIcon = 'fa-ticket-alt';
                        $statusLabel = __('Ticketed');
                    } elseif (in_array($booking->status, ['confirmed', 'paid', 'completed'])) {
                        $statusBadgeClass = 'bg-success';
                        $statusIcon = 'fa-check-circle';
                        $statusLabel = __('Confirmed');
                    }
                @endphp
                <span class="badge {{ $statusBadgeClass }}" style="padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fas {{ $statusIcon }} me-1"></i>
                    {{ $statusLabel }}
                </span>
                @if($isRoundTrip)
                    <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; padding: 6px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem;">
                        <i class="fas fa-sync-alt me-1"></i> {{ __('Round Trip') }}
                    </span>
                @else
                    <span class="badge" style="background: rgba(255,255,255,0.15); color: #fff; padding: 6px 10px; border-radius: 8px; font-weight: 700; font-size: 0.75rem;">
                        <i class="fas fa-long-arrow-alt-{{ app()->isLocale('ar') ? 'left' : 'right' }} me-1"></i> {{ __('One Way') }}
                    </span>
                @endif
            </div>
            <span class="pass-pnr" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 900;">
                <i class="fas fa-barcode"></i>
                <span>{{ __('Airline PNR') }}: <strong>{{ $pnrDisplay }}</strong></span>
            </span>
        </div>
        <div class="pass-body">
            {{-- Outbound Route --}}
            <div style="font-size: 0.8rem; font-weight: 800; color: var(--primary-blue); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                <i class="fas fa-plane-departure me-1"></i> {{ __('Outbound Flight') }}
            </div>
            <div class="pass-airport-row" style="{{ $isRoundTrip ? 'margin-bottom: 16px;' : 'margin-bottom: 24px;' }}">
                <div class="pass-airport-code">
                    <h3 class="notranslate" translate="no">{{ strtoupper(substr($originCode, 0, 3)) }}</h3>
                    <span style="font-size: 0.8rem; line-height: 1.2;">{{ $originName }}</span>
                </div>
                <div class="pass-path-line">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="pass-airport-code dest-code">
                    <h3 class="notranslate" translate="no">{{ strtoupper(substr($destCode, 0, 3)) }}</h3>
                    <span style="font-size: 0.8rem; line-height: 1.2;">{{ $destName }}</span>
                </div>
            </div>

            {{-- Return Route (If Round Trip) --}}
            @if($isRoundTrip)
                <div style="border-top: 1px dashed var(--border-color); padding-top: 16px; margin-top: 8px;">
                    <div style="font-size: 0.8rem; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                        <i class="fas fa-plane-arrival me-1"></i> {{ __('Return Flight') }}
                    </div>
                    <div class="pass-airport-row" style="margin-bottom: 24px;">
                        <div class="pass-airport-code">
                            <h3 class="notranslate" translate="no" style="color: #b45309;">{{ strtoupper(substr($destCode, 0, 3)) }}</h3>
                            <span style="font-size: 0.8rem; line-height: 1.2;">{{ $destName }}</span>
                        </div>
                        <div class="pass-path-line">
                            <i class="fas fa-plane" style="color: #b45309;"></i>
                        </div>
                        <div class="pass-airport-code dest-code">
                            <h3 class="notranslate" translate="no" style="color: #b45309;">{{ strtoupper(substr($originCode, 0, 3)) }}</h3>
                            <span style="font-size: 0.8rem; line-height: 1.2;">{{ $originName }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="boarding-pass-stub-line">
                <div class="pass-details-grid" style="{{ $isRoundTrip ? 'grid-template-columns: repeat(5, 1fr);' : '' }}">
                    <div>
                        <span class="pass-label">{{ __('PASSENGER') }}</span>
                        <span class="pass-val">{{ auth()->user()->full_name ?? ($booking->user->full_name ?? ($booking->user->name ?? 'Guest')) }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('BOOKING DATE') }}</span>
                        <span class="pass-val">{{ $booking->created_at->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="pass-label">{{ __('DEPARTURE DATE') }}</span>
                        <span class="pass-val">{{ $exactDepartureTime ? $exactDepartureTime->format('d M Y') : ($booking->flightBooking->departure_date ? $booking->flightBooking->departure_date->format('d M Y') : 'N/A') }}</span>
                    </div>
                    @if($isRoundTrip)
                    <div>
                        <span class="pass-label" style="color: #b45309;">{{ __('RETURN DATE') }}</span>
                        <span class="pass-val" style="color: #b45309; font-weight: 800;">{{ $exactReturnTime ? $exactReturnTime->format('d M Y') : 'N/A' }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="pass-label">{{ __('CLASS') }}</span>
                        <span class="pass-val">{{ $booking->flightBooking->flight_class ?? __('Economy') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="booking-detail-grid">

        {{-- LEFT COLUMN --}}
        <div>
            
            {{-- Itinerary Card --}}
            <div class="detail-card mb-4" style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
                <div class="detail-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 20px 24px; border-bottom: 1px solid var(--border-color);">
                    <h5 style="margin: 0; font-size: 1.15rem; font-weight: 850; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-route text-primary" style="font-size: 1.25rem;"></i> {{ __('تفاصيل ومسار الرحلة') }}
                    </h5>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="badge" style="background: rgba(37, 99, 235, 0.08); color: var(--primary-blue); font-weight: 800; font-size: 0.82rem; padding: 6px 12px; border-radius: 8px;">
                            <i class="fas fa-plane me-1"></i> {{ $booking->airline_name ?? __('طيران') }} ({{ $booking->airline_code ?? 'Flight' }})
                        </span>
                        @if($booking->flightBooking->flight_class)
                            <span class="badge" style="background: rgba(16, 185, 129, 0.08); color: #10b981; font-weight: 800; font-size: 0.82rem; padding: 6px 12px; border-radius: 8px;">
                                <i class="fas fa-chair me-1"></i> {{ $booking->flightBooking->flight_class }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="detail-card-body" style="padding: 24px;">
                    @php
                        $isAr = app()->isLocale('ar');
                    @endphp

                    @if(!empty($legs))
                        @foreach($legs as $legIdx => $legSegs)
                            @php
                                $isReturnLeg = ($legIdx === 1 || ($totalLegsCount > 1 && $legIdx > 0));
                                $stopsCount = max(0, count($legSegs) - 1);
                                
                                $firstSeg = $legSegs[0] ?? [];
                                $lastSeg = end($legSegs) ?: [];
                                
                                $lDepCode = $firstSeg['DepartureAirportLocationCode'] ?? ($firstSeg['DepartureAirport']['LocationCode'] ?? ($firstSeg['from'] ?? 'N/A'));
                                $lArrCode = $lastSeg['ArrivalAirportLocationCode'] ?? ($lastSeg['ArrivalAirport']['LocationCode'] ?? ($lastSeg['to'] ?? 'N/A'));
                                
                                $lDepAir = \App\Models\Airport::where('airport_code', $lDepCode)->first();
                                $lArrAir = \App\Models\Airport::where('airport_code', $lArrCode)->first();
                                
                                $lDepCity = ($isAr && !empty($lDepAir->city_name_ar)) ? $lDepAir->city_name_ar : ($lDepAir->city_name ?? $lDepCode);
                                $lArrCity = ($isAr && !empty($lArrAir->city_name_ar)) ? $lArrAir->city_name_ar : ($lArrAir->city_name ?? $lArrCode);
                                
                                $legTitle = $isReturnLeg ? __('رحلة العودة (Return Flight)') : __('رحلة الذهاب (Outbound Flight)');
                                $legColor = $isReturnLeg ? '#b45309' : 'var(--primary-blue)';
                                $legBg = $isReturnLeg ? 'rgba(245, 158, 11, 0.04)' : 'rgba(37, 99, 235, 0.03)';
                                $legBorder = $isReturnLeg ? 'rgba(245, 158, 11, 0.2)' : 'rgba(37, 99, 235, 0.15)';
                            @endphp

                            <div class="itin-leg-card" style="background: {{ $legBg }}; border: 1.5px solid {{ $legBorder }};">
                                {{-- Leg Header --}}
                                <div class="itin-leg-header">
                                    <div class="itin-leg-title" style="color: {{ $legColor }};">
                                        <i class="fas {{ $isReturnLeg ? 'fa-plane-arrival' : 'fa-plane-departure' }}"></i>
                                        <span>{{ $legTitle }}:</span>
                                        <span class="notranslate" translate="no" style="font-weight: 900; letter-spacing: 0.5px;">{{ strtoupper(substr($lDepCode, 0, 3)) }}</span>
                                        <i class="fas fa-long-arrow-alt-{{ $isAr ? 'left' : 'right' }} mx-1"></i>
                                        <span class="notranslate" translate="no" style="font-weight: 900; letter-spacing: 0.5px;">{{ strtoupper(substr($lArrCode, 0, 3)) }}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        @if($stopsCount === 0)
                                            <span class="itin-leg-badge badge-direct">
                                                <i class="fas fa-check-circle"></i> {{ __('رحلة مباشرة بدون توقف') }}
                                            </span>
                                        @elseif($stopsCount === 1)
                                            <span class="itin-leg-badge badge-transit">
                                                <i class="fas fa-pause-circle"></i> {{ __('محطة توقف واحدة (ترانزيت)') }}
                                            </span>
                                        @else
                                            <span class="itin-leg-badge badge-transit">
                                                <i class="fas fa-pause-circle"></i> {{ __(':count محطات توقف', ['count' => $stopsCount]) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Timeline Segments --}}
                                <div class="itin-timeline">
                                    @foreach($legSegs as $sIdx => $seg)
                                        @php
                                            $sDepCode = $seg['DepartureAirportLocationCode'] ?? ($seg['DepartureAirport']['LocationCode'] ?? ($seg['from'] ?? 'N/A'));
                                            $sArrCode = $seg['ArrivalAirportLocationCode'] ?? ($seg['ArrivalAirport']['LocationCode'] ?? ($seg['to'] ?? 'N/A'));
                                            
                                            $sDepAir = \App\Models\Airport::where('airport_code', $sDepCode)->first();
                                            $sArrAir = \App\Models\Airport::where('airport_code', $sArrCode)->first();
                                            
                                            $sDepCity = ($isAr && !empty($sDepAir->city_name_ar)) ? $sDepAir->city_name_ar : ($sDepAir->city_name ?? $sDepCode);
                                            $sArrCity = ($isAr && !empty($sArrAir->city_name_ar)) ? $sArrAir->city_name_ar : ($sArrAir->city_name ?? $sArrCode);
                                            $sDepName = ($isAr && !empty($sDepAir->airport_name_ar)) ? $sDepAir->airport_name_ar : ($sDepAir->airport_name ?? '');
                                            $sArrName = ($isAr && !empty($sArrAir->airport_name_ar)) ? $sArrAir->airport_name_ar : ($sArrAir->airport_name ?? '');

                                            $sDepDtRaw = $seg['DepartureDateTime'] ?? ($seg['dep_datetime'] ?? null);
                                            $sArrDtRaw = $seg['ArrivalDateTime'] ?? ($seg['arr_datetime'] ?? null);
                                            $sDepDt = null; $sArrDt = null;
                                            if ($sDepDtRaw) { try { $sDepDt = \Carbon\Carbon::parse($sDepDtRaw); } catch (\Exception $e) {} }
                                            if ($sArrDtRaw) { try { $sArrDt = \Carbon\Carbon::parse($sArrDtRaw); } catch (\Exception $e) {} }
                                            
                                            $segDur = ($sDepDt && $sArrDt) ? $sDepDt->diff($sArrDt)->format($isAr ? '%hس %iد' : '%hh %im') : null;
                                            $flNum = $seg['FlightNumber'] ?? ($seg['flight_number'] ?? null);
                                            $airlineName = $seg['MarketingAirlineName'] ?? ($seg['MarketingAirlineCode'] ?? ($booking->airline_name ?? 'Flight'));
                                            $baggage = $seg['Baggage'] ?? ($seg['baggage'] ?? null);
                                            $layover = $seg['Layover'] ?? ($seg['layover'] ?? null);
                                        @endphp

                                        <div class="itin-segment-block">
                                            {{-- Departure Row --}}
                                            <div class="itin-point-row">
                                                <div class="itin-point-time">
                                                    <div class="itin-time-main">{{ $sDepDt ? $sDepDt->format('H:i') : '--:--' }}</div>
                                                    <div class="itin-time-sub">{{ $sDepDt ? $sDepDt->translatedFormat('d M Y') : '' }}</div>
                                                </div>
                                                <div class="itin-point-node"></div>
                                                <div class="itin-point-info">
                                                    <div class="itin-airport-city">
                                                        <span>{{ $sDepCity }}</span>
                                                        <span class="itin-code-pill notranslate" translate="no">{{ strtoupper(substr($sDepCode, 0, 3)) }}</span>
                                                    </div>
                                                    @if($sDepName)
                                                        <div class="itin-airport-name">{{ $sDepName }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Flight Connector Row --}}
                                            <div class="itin-connector-wrapper">
                                                <div class="itin-connector-time-spacer"></div>
                                                <div class="itin-connector-line-col">
                                                    <div class="itin-connector-line"></div>
                                                </div>
                                                <div class="itin-flight-badge-card">
                                                    <div class="itin-flight-meta">
                                                        <div class="itin-flight-airline">
                                                            <i class="fas fa-plane" style="color: var(--primary-blue);"></i>
                                                            <span>{{ $airlineName }}</span>
                                                            @if($flNum)
                                                                <span class="notranslate" translate="no" style="font-weight: 900; color: var(--primary-blue);">{{ $flNum }}</span>
                                                            @endif
                                                        </div>
                                                        @if($baggage)
                                                            <div class="itin-flight-chip">
                                                                <i class="fas fa-suitcase-rolling me-1 text-primary"></i> {{ __('وزن مسجل:') }} {{ $baggage }}
                                                            </div>
                                                        @endif
                                                        <div class="itin-flight-chip">
                                                            <i class="fas fa-chair me-1"></i> {{ $booking->flightBooking->flight_class ?? __('درجة اقتصادية') }}
                                                        </div>
                                                    </div>
                                                    @if($segDur)
                                                        <div class="itin-flight-duration">
                                                            <i class="far fa-clock"></i>
                                                            <span>{{ __('مدة الرحلة:') }} {{ $segDur }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Arrival Row --}}
                                            <div class="itin-point-row">
                                                <div class="itin-point-time">
                                                    <div class="itin-time-main">{{ $sArrDt ? $sArrDt->format('H:i') : '--:--' }}</div>
                                                    <div class="itin-time-sub">{{ $sArrDt ? $sArrDt->translatedFormat('d M Y') : '' }}</div>
                                                </div>
                                                <div class="itin-point-node node-dest"></div>
                                                <div class="itin-point-info">
                                                    <div class="itin-airport-city">
                                                        <span>{{ $sArrCity }}</span>
                                                        <span class="itin-code-pill notranslate" translate="no" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">{{ strtoupper(substr($sArrCode, 0, 3)) }}</span>
                                                    </div>
                                                    @if($sArrName)
                                                        <div class="itin-airport-name">{{ $sArrName }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Transit / Layover Box (Between segments in same leg) --}}
                                        @if(!$loop->last)
                                            @php
                                                $nextSeg = $legSegs[$sIdx + 1] ?? null;
                                                $layoverTimeStr = $layover;
                                                if (!$layoverTimeStr && $nextSeg && isset($nextSeg['DepartureDateTime']) && $sArrDt) {
                                                    try {
                                                        $nextDep = \Carbon\Carbon::parse($nextSeg['DepartureDateTime']);
                                                        $layoverTimeStr = $sArrDt->diff($nextDep)->format($isAr ? '%hس %iد' : '%hh %im');
                                                    } catch (\Exception $e) {}
                                                }
                                            @endphp
                                            <div class="itin-layover-wrapper">
                                                <div class="itin-layover-spacer"></div>
                                                <div class="itin-layover-dot-col">
                                                    <div class="itin-layover-dot"></div>
                                                </div>
                                                <div class="itin-layover-card">
                                                    <div class="itin-layover-title">
                                                        <i class="fas fa-mug-hot"></i>
                                                        <span>{{ __('ترانزيت في') }} {{ $sArrCity }} (<span class="notranslate" translate="no">{{ strtoupper(substr($sArrCode, 0, 3)) }}</span>)</span>
                                                    </div>
                                                    <div class="itin-layover-sub">
                                                        @if($layoverTimeStr)
                                                            <i class="far fa-hourglass me-1"></i> {{ __('مدة الانتظار:') }} <strong style="font-size: 0.9rem;">{{ $layoverTimeStr }}</strong> • 
                                                        @endif
                                                        <span>{{ __('تغيير الطائرة في نفس المطار') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback if no legs parsed --}}
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> {{ __('لا توجد تفاصيل إضافية لمسار الرحلة.') }}
                        </div>
                    @endif

                    @if($exactDepartureTime && $exactDepartureTime->isFuture())
                        <div class="info-row mt-3 p-3 rounded" style="background: rgba(16, 185, 129, 0.08); border-inline-start: 4px solid var(--fd-success); border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <span class="info-label text-success" style="font-weight: 800;"><i class="fas fa-stopwatch me-1"></i> {{ __('الوقت المتبقي على الإقلاع') }}</span>
                            <span class="info-value fw-bold text-success" style="font-size: 1.15rem;">{{ $exactDepartureTime->diffForHumans(['parts' => 2, 'short' => false]) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Cancellation & Refund Details Card (Shown if booking is cancelled) --}}
            @if($booking->status === 'cancelled')
                @php
                    $refAmount = (float)($booking->refund_amount ?? ($refundTransaction ? $refundTransaction->amount : 0.0));
                    $cancelFee = (float)($booking->cancellation_fee ?? max(0, (float)$booking->total_amount - $refAmount));
                    $cancelDate = $booking->cancelled_at ?? ($refundTransaction ? $refundTransaction->created_at : $booking->updated_at);
                @endphp
                <div class="detail-card cancellation-report-card" style="border: 2px solid #fecaca; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(239, 68, 68, 0.08); margin-bottom: 24px;">
                    <div style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); padding: 18px 24px; border-bottom: 1.5px solid #fecaca; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 46px; height: 46px; border-radius: 14px; background: #ef4444; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <h5 style="margin: 0; font-weight: 800; color: #991b1b; font-size: 1.15rem;">{{ __('تفاصيل إلغاء الحجز والاسترداد المالي') }}</h5>
                                <span style="font-size: 0.8rem; color: #b91c1c; font-weight: 600;">
                                    <i class="far fa-clock me-1"></i> {{ __('تاريخ الإلغاء:') }} {{ $cancelDate ? $cancelDate->format('d M Y, h:i A') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <span class="badge" style="background: #dc2626; color: white; font-weight: 800; font-size: 0.82rem; padding: 7px 16px; border-radius: 30px; letter-spacing: 0.5px;">
                            <i class="fas fa-times-circle me-1"></i> {{ __('ملغي ومسترد') }}
                        </span>
                    </div>

                    <div style="padding: 24px;">
                        {{-- 3-Stat Financial Grid --}}
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px; margin-bottom: 20px;">
                            <!-- 1. Total Paid -->
                            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px;">
                                <div style="font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-receipt text-primary"></i> {{ __('المبلغ الأصلي المدفوع') }}
                                </div>
                                <div style="font-size: 1.45rem; font-weight: 900; color: #0f172a;">
                                    {{ number_format($booking->total_amount, 2) }} <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">{{ $booking->currency }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">{{ __('قيمة التذكرة والرسوم الكاملة') }}</div>
                            </div>

                            <!-- 2. Cancellation Penalty -->
                            <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 14px; padding: 16px;">
                                <div style="font-size: 0.78rem; font-weight: 700; color: #c2410c; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-hand-holding-usd text-warning"></i> {{ __('غرامة ورسوم الإلغاء') }}
                                </div>
                                <div style="font-size: 1.45rem; font-weight: 900; color: #ea580c;">
                                    -{{ number_format($cancelFee, 2) }} <span style="font-size: 0.85rem; font-weight: 700; color: #c2410c;">{{ $booking->currency }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: #9a3412; margin-top: 4px;">{{ __('مخصومة وفق شروط وسياسة الطيران') }}</div>
                            </div>

                            <!-- 3. Refunded to Wallet (High Contrast & Crystal Clear) -->
                            <div class="cancellation-metric-card cancellation-metric-wallet" style="background: #ffffff; border: 2.5px solid #10b981; border-radius: 16px; padding: 18px; box-shadow: 0 4px 18px rgba(16, 185, 129, 0.15);">
                                <div class="metric-title" style="font-size: 0.8rem; font-weight: 850; color: #047857; text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                                    <span style="display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-wallet text-success" style="font-size: 1rem;"></i> {{ __('المبلغ المسترد للمحفظة') }}
                                    </span>
                                    <span class="badge" style="background: #dcfce7; color: #14532d; font-size: 0.72rem; font-weight: 850; padding: 4px 9px; border-radius: 6px; border: 1px solid #86efac;">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('تم الإيداع') }}
                                    </span>
                                </div>
                                <div class="metric-amount" style="font-size: 1.65rem; font-weight: 950; color: #064e3b; letter-spacing: -0.5px; line-height: 1.2;">
                                    +{{ number_format($refAmount, 2) }} <span class="metric-currency" style="font-size: 0.95rem; font-weight: 850; color: #047857;">{{ $booking->currency }}</span>
                                </div>
                                <div class="metric-sub" style="font-size: 0.8rem; font-weight: 750; color: #047857; margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-arrow-circle-down text-success"></i> {{ __('أودع بنجاح في رصيد محفظتك بالمنصة') }}
                                </div>
                            </div>
                        </div>

                        {{-- Wallet Status Confirmation Box --}}
                        @if($refAmount > 0)
                            <div class="wallet-confirm-box" style="background: #f0fdf4; border: 2px solid #86efac; border-radius: 16px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.08);">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="width: 50px; height: 50px; border-radius: 14px; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 900; color: #064e3b; font-size: 1.05rem; margin-bottom: 4px;">
                                            {{ __('تم إيداع المبلغ المسترد في محفظتك الإلكترونية بنجاح') }}
                                        </div>
                                        <div style="font-size: 0.88rem; color: #166534; font-weight: 600; line-height: 1.5;">
                                            {{ __('تمت إضافة مبلغ') }} <span style="font-size: 1.05rem; font-weight: 950; color: #064e3b; background: #dcfce7; padding: 2px 10px; border-radius: 6px; border: 1.5px solid #86efac;">{{ number_format($refAmount, 2) }} {{ $booking->currency }}</span> {{ __('إلى رصيد محفظتك الرقمية ويمكنك استخدامه فوراً في أي حجز جديد.') }}
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('customer.wallet.index') }}" class="btn btn-sm btn-success" style="padding: 10px 24px; border-radius: 12px; font-weight: 850; font-size: 0.9rem; white-space: nowrap; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                                    <i class="fas fa-wallet me-1"></i> {{ __('عرض المحفظة والرصيد') }}
                                </a>
                            </div>
                        @else
                            <div style="background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-info-circle text-warning fs-4"></i>
                                <div style="font-size: 0.86rem; color: #9a3412;">
                                    <strong>{{ __('سياسة الاسترداد:') }}</strong> {{ __('خضعت هذه التذكرة لغرامة إلغاء بنسبة 100% وفق شروط شركة الطيران المعتمدة، لذا لم يتم إرجاع مبالغ مالية للمحفظة.') }}
                                </div>
                            </div>
                        @endif

                        @if(!empty($booking->cancellation_reason))
                            <div style="margin-top: 14px; font-size: 0.82rem; color: #64748b; background: #f8fafc; padding: 10px 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <strong style="color: #334155;"><i class="fas fa-info-circle text-primary me-1"></i> {{ __('ملاحظة الإلغاء:') }}</strong> {{ $booking->cancellation_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Passengers Section --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <h5><i class="fas fa-users"></i> {{ __('Travelers') }}</h5>
                    <span class="badge" style="background: var(--primary-blue); color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem;">
                        {{ $booking->passengers->count() }} {{ __('Total') }}
                    </span>
                </div>
                <div class="detail-card-body" style="padding: 16px;">
                    @forelse($booking->passengers as $pax)
                        <div class="passenger-item">
                            <div class="passenger-main">
                                <div class="passenger-avatar">
                                    <i class="fas {{ $pax->passenger_type === 'infant' ? 'fa-baby' : ($pax->passenger_type === 'child' ? 'fa-child' : 'fa-user') }}"></i>
                                </div>
                                <div class="passenger-info">
                                    <span class="p-name">{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</span>
                                    <span class="p-meta text-uppercase">
                                        {{ __($pax->passenger_type ?? 'adult') }}
                                    </span>
                                </div>
                            </div>
                            <div class="passenger-docs">
                                @if($pax->e_ticket_no && in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']))
                                    <div class="ticket-tag" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); padding: 8px 14px; border-radius: 10px; flex: 1; min-width: 140px;">
                                        <label style="color: #10b981; font-size: 0.65rem;"><i class="fas fa-ticket-alt me-1"></i>{{ __('E-Ticket No') }}</label>
                                        <strong style="color: #047857; font-size: 1.05rem; letter-spacing: 0.5px;">{{ $pax->e_ticket_no }}</strong>
                                    </div>
                                @endif
                                @if($pax->passport_number)
                                    <div class="passport-tag" style="background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.2); padding: 8px 14px; border-radius: 10px; flex: 1; min-width: 140px;">
                                        <label style="color: #3b82f6; font-size: 0.65rem;"><i class="fas fa-passport me-1"></i>{{ __('Passport') }}</label>
                                        <strong style="color: #1d4ed8; font-size: 1.05rem; letter-spacing: 0.5px;">{{ $pax->passport_number }}</strong>
                                    </div>
                                @endif
                                @if($pax->passport_expiry)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-calendar-times me-1"></i>{{ __('Expiry') }}</label>
                                        <strong>{{ $pax->passport_expiry->format('d M Y') }}</strong>
                                    </div>
                                @endif
                                @if($pax->dob)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-birthday-cake me-1"></i>{{ __('DOB') }}</label>
                                        <strong>{{ $pax->dob->format('d M Y') }}</strong>
                                    </div>
                                @endif
                                @if($pax->nationality)
                                    <div class="passport-tag" style="padding: 8px 14px; border-radius: 10px; min-width: 100px;">
                                        <label><i class="fas fa-globe-americas me-1"></i>{{ __('Nationality') }}</label>
                                        <strong>{{ $pax->nationality }}</strong>
                                    </div>
                                @endif
                            </div>

                            @php
                                $bookingExtras = \App\Helpers\FlightExtraServiceHelper::getBookingAllExtras($booking);
                                $allPassengersExtras = $bookingExtras['passengers'] ?? [];
                                $paxExtras = $allPassengersExtras[$loop->index] ?? [];
                            @endphp

                            @if(!empty($paxExtras))
                                <div class="passenger-extra-services" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed rgba(0,0,0,0.1);">
                                    <h6 style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px; font-weight: 700;">
                                        <i class="fas fa-plus-circle me-1" style="color: var(--primary-blue)"></i> {{ __('Extra Services') }}
                                    </h6>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        @foreach($paxExtras as $extra)
                                            <div class="extra-service-badge" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                                <i class="fas {{ $extra['icon'] ?? 'fa-plus' }}" style="color: var(--primary-blue);"></i>
                                                <span style="color: #334155;">{{ $extra['title'] ?? $extra['description'] ?? $extra['code'] }}</span>
                                                @if(isset($extra['price']) && (float)$extra['price'] > 0)
                                                    <span style="color: #10b981; font-weight: 700; margin-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: auto;">+{{ number_format($extra['price'], 2) }} {{ $extra['currency'] ?? 'SAR' }}</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-auto" style="font-size: 0.72rem;">{{ __('Included / Selected') }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-user-slash d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            {{ __('No passenger details found.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Journey Timeline Segments --}}
            @php
                $itinData = $booking->flightBooking->itinerary_data ?? [];
                $legs = [];
                if (isset($itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'])) {
                    $options = $itinData['FareItineraries']['FareItinerary']['OriginDestinationOptions'];
                    if (isset($options['OriginDestinationOption']['FlightSegment'])) {
                        $options = [$options['OriginDestinationOption']];
                    } else {
                        $options = $options['OriginDestinationOption'] ?? [];
                    }
                    
                    foreach($options as $legIndex => $opt) {
                        $segs = $opt['FlightSegment'] ?? $opt;
                        $legSegments = [];
                        if (isset($segs['FlightNumber'])) { 
                            $legSegments[] = $segs; 
                        } else { 
                            foreach($segs as $s) { 
                                $legSegments[] = $s['FlightSegment'] ?? $s; 
                            } 
                        }
                        if (!empty($legSegments)) {
                            $legs[] = $legSegments;
                        }
                    }
                }
            @endphp

            @if(!empty($legs))
                @foreach($legs as $legIndex => $segments)
                    <div class="detail-card mb-4">
                        <div class="detail-card-header">
                            <h5><i class="fas fa-route"></i> {{ __('Detailed Journey') }} - {{ $legIndex == 0 ? __('Outbound Flight') : __('Return Flight') }}</h5>
                        </div>
                        <div class="segment-timeline">
                            @foreach($segments as $idx => $seg)
                                @php
                                    $depTime = isset($seg['DepartureDateTime']) ? \Carbon\Carbon::parse($seg['DepartureDateTime']) : now();
                                    $arrTime = isset($seg['ArrivalDateTime']) ? \Carbon\Carbon::parse($seg['ArrivalDateTime']) : now();
                                    $airlineCode = $seg['MarketingAirlineCode'] ?? $seg['OperatedByAirlineCode'] ?? '';
                                    
                                    $depCode = $seg['DepartureAirportLocationCode'] ?? ($seg['DepartureAirport']['LocationCode'] ?? '');
                                    $arrCode = $seg['ArrivalAirportLocationCode'] ?? ($seg['ArrivalAirport']['LocationCode'] ?? '');
                                    
                                    $depAir = \App\Models\Airport::where('airport_code', $depCode)->first();
                                    $arrAir = \App\Models\Airport::where('airport_code', $arrCode)->first();
                                @endphp
                                <div class="segment-step">
                                    <div class="seg-dot"><i class="fas fa-circle" style="font-size: 0.4rem;"></i></div>
                                    <div class="seg-airline">
                                        <img src="https://travelnext.works/api/airlines/{{ $airlineCode }}.gif" 
                                             onerror="this.src='https://via.placeholder.com/30?text={{ $airlineCode }}'"
                                             alt="{{ $airlineCode }}">
                                        <span>{{ $airlineCode }} {{ $seg['FlightNumber'] ?? '' }}</span>
                                    </div>
                                    <div class="seg-main">
                                        <div class="seg-point">
                                            <div class="time">{{ $depTime->format('H:i') }}</div>
                                            <div class="airport">
                                                <strong>{{ $depCode }}</strong>
                                                <div style="font-size:0.75rem; color:#64748b; line-height:1.2;">{{ $depAir ? $depAir->city_name : '' }}</div>
                                            </div>
                                            <div class="date">{{ $depTime->format('d M, Y') }}</div>
                                        </div>
                                        <div class="seg-path">
                                            <div class="seg-path-line"></div>
                                            <i class="fas fa-plane"></i>
                                            <div class="path-dur">{{ $depTime->diff($arrTime)->format('%hh %im') }}</div>
                                        </div>
                                        <div class="seg-point text-end">
                                            <div class="time">{{ $arrTime->format('H:i') }}</div>
                                            <div class="airport">
                                                <strong>{{ $arrCode }}</strong>
                                                <div style="font-size:0.75rem; color:#64748b; line-height:1.2;">{{ $arrAir ? $arrAir->city_name : '' }}</div>
                                            </div>
                                            <div class="date">{{ $arrTime->format('d M, Y') }}</div>
                                        </div>
                                    </div>
                                    @if(isset($seg['ResBookDesigCode']))
                                        <div class="seg-footer">
                                            <span><i class="fas fa-couch"></i> {{ __('Class') }}: {{ $seg['ResBookDesigCode'] }}</span>
                                            @if(isset($seg['AdjustmentTime']))
                                                <span class="ms-3"><i class="fas fa-clock"></i> {{ __('Technical Stop') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="fd-sidebar">
            
            {{-- Price Breakdown Stub --}}
            <div class="summary-total">
                <div class="amount-label">{{ __('Total Price') }}</div>
                <div class="amount">{{ number_format($booking->total_amount, 2) }} <span>{{ $booking->currency }}</span></div>
                
                <div class="price-breakdown">
                    <div class="row-info">
                        <span>{{ __('Base Fare') }}</span>
                        <strong>{{ number_format(max(0, $booking->total_amount - ($booking->insurance_amount ?? 0)), 2) }} {{ $booking->currency }}</strong>
                    </div>
                    @if(($booking->insurance_amount ?? 0) > 0)
                    <div class="row-info" style="color: #10b981; font-weight: 700;">
                        <span><i class="fas fa-shield-alt"></i> {{ __('Travel Insurance') }}</span>
                        <strong>+ {{ number_format($booking->insurance_amount, 2) }} {{ $booking->currency }}</strong>
                    </div>
                    @endif
                    <div class="row-info">
                        <span>{{ __('Taxes & Fees') }}</span>
                        <strong>0.00 {{ $booking->currency }}</strong>
                    </div>
                    @if($booking->status === 'cancelled')
                        <div class="row-info" style="color: #ea580c; font-weight: 700; border-top: 1px dashed #e2e8f0; padding-top: 8px; margin-top: 8px;">
                            <span><i class="fas fa-hand-holding-usd me-1"></i> {{ __('غرامة الإلغاء') }}</span>
                            <strong>-{{ number_format($cancelFee, 2) }} {{ $booking->currency }}</strong>
                        </div>
                        <div class="row-info" style="color: #15803d; font-weight: 800; font-size: 0.95rem; background: #f0fdf4; padding: 6px 10px; border-radius: 8px; margin-top: 6px; border: 1px solid #bbf7d0;">
                            <span><i class="fas fa-wallet me-1"></i> {{ __('أودع بالمحفظة') }}</span>
                            <strong>+{{ number_format($refAmount, 2) }} {{ $booking->currency }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Travel Insurance Card --}}
            @if(($booking->insurance_amount ?? 0) > 0 || $booking->insurancePolicy)
            @php $policy = $booking->insurancePolicy; @endphp
            <div class="detail-card" style="border: 1.5px solid #10b981; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);">
                <div class="detail-card-header" style="background: transparent; border-bottom: 1px solid #dcfce7;">
                    <h5 style="color: #15803d; font-size: 0.95rem; margin:0;"><i class="fas fa-shield-alt text-success me-2"></i> {{ __('Travel Insurance Protected') }}</h5>
                    <span class="badge bg-success" style="font-size: 0.75rem;">{{ __('Active') }}</span>
                </div>
                <div class="detail-card-body" style="padding: 16px;">
                    <div style="font-size: 0.83rem; color: #334155; margin-bottom: 12px;">
                        <div><strong style="color:#0f172a;">{{ __('Policy Number:') }}</strong> <span class="font-monospace text-primary fw-bold">{{ $policy->policy_number ?? ('POL-'.strtoupper(substr(md5($booking->id), 0, 8))) }}</span></div>
                        <div class="mt-1"><strong style="color:#0f172a;">{{ __('Coverage Limit:') }}</strong> <span class="text-success fw-bold">$500,000 USD</span></div>
                        <div class="mt-1 text-muted" style="font-size: 0.76rem;">{{ __('Emergency Medical, Trip Delay & Baggage Protection') }}</div>
                    </div>
                    @if($policy)
                    <a href="{{ route('customer.insurances.certificate', $policy->id) }}" class="btn btn-sm btn-outline-success w-100 fw-bold" target="_blank" style="border-radius: 8px;">
                        <i class="fas fa-file-pdf me-1"></i> {{ __('Download Insurance Certificate') }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Action Stack --}}
            <div class="detail-card">
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="actions-stack">
                        @if($booking->status === 'pending')
                            @php
                                $isExpired = $booking->ticketing_time_limit && now()->greaterThan($booking->ticketing_time_limit);
                            @endphp
                            @if($isExpired)
                                <div class="expired-msg-sidebar">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>{{ __('انتهت مهلة الدفع الخاصة بهذا الحجز.') }}</span>
                                </div>
                                <a href="{{ route('customer.bookings.flights.quick-rebook', $booking->id) }}" class="action-btn action-btn-primary" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                                    <i class="fas fa-sync-alt"></i> {{ __('إعادة الحجز السريع') }}
                                </a>
                                <a href="{{ route('flights') }}" class="action-btn action-btn-outline">
                                    <i class="fas fa-search"></i> {{ __('بحث رحلة جديدة') }}
                                </a>
                            @else
                                <a href="{{ route('flights.payment.select', $booking->id) }}" class="action-btn action-btn-primary" id="payButton">
                                    <i class="fas fa-credit-card"></i> {{ __('Pay Now') }}
                                </a>
                            @endif
                        @elseif($booking->status === 'cancelled')
                            <div class="expired-msg-sidebar" style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; padding: 14px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-times-circle" style="color: #ef4444; font-size: 1.3rem; flex-shrink: 0;"></i>
                                <div style="line-height: 1.4;">
                                    <strong style="display: block; font-size: 0.88rem;">{{ __('هذا الحجز ملغي') }}</strong>
                                    <span style="font-size: 0.76rem; color: #b91c1c;">{{ __('تمت تسوية الإلغاء واسترداد المبلغ') }}</span>
                                </div>
                            </div>
                            @if($refAmount > 0)
                                <a href="{{ route('customer.wallet.index') }}" class="action-btn action-btn-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <i class="fas fa-wallet"></i> {{ __('عرض رصيد المحفظة (+:amount :curr)', ['amount' => number_format($refAmount, 2), 'curr' => $booking->currency]) }}
                                </a>
                            @endif
                            <a href="{{ route('flights') }}" class="action-btn action-btn-outline">
                                <i class="fas fa-search"></i> {{ __('بحث رحلة جديدة') }}
                            </a>
                        @elseif(in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']))
                            <a href="{{ route('customer.bookings.invoice', ['id' => $booking->id, 'type' => 'flight']) }}" class="action-btn action-btn-success" target="_blank">
                                <i class="fas fa-file-invoice-dollar"></i> {{ __('Download Voucher') }}
                            </a>
                        @endif
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '') }}" class="action-btn action-btn-outline" target="_blank">
                            <i class="fab fa-whatsapp" style="color: #25d366;"></i> {{ __('WhatsApp Support') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Booking Timeline --}}
            <div class="detail-card">
                <div class="detail-card-header" style="padding: 16px 20px;">
                    <h5><i class="fas fa-history"></i> {{ __('Booking Timeline') }}</h5>
                </div>
                <div class="detail-card-body" style="padding: 20px;">
                    <div class="timeline-v">
                        <div class="tl-item done">
                            <div class="tl-dot"><i class="fas fa-check"></i></div>
                            <div class="tl-text">
                                <strong>{{ __('Reservation Created') }}</strong>
                                <small>{{ $booking->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @if($booking->status === 'cancelled')
                            <div class="tl-item done">
                                <div class="tl-dot" style="background: #10b981; color: white;"><i class="fas fa-check"></i></div>
                                <div class="tl-text">
                                    <strong>{{ __('Payment Completed') }}</strong>
                                    <small>{{ $booking->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="tl-item done">
                                <div class="tl-dot" style="background: #ef4444; color: white;"><i class="fas fa-ban"></i></div>
                                <div class="tl-text">
                                    <strong style="color: #b91c1c;">{{ __('Booking Cancelled & Refunded') }}</strong>
                                    <small>{{ $cancelDate ? $cancelDate->format('d M Y, H:i') : '' }}</small>
                                </div>
                            </div>
                        @else
                            <div class="tl-item {{ in_array($booking->status, ['paid', 'confirmed', 'ticketed', 'completed']) ? 'done' : '' }}">
                                <div class="tl-dot">
                                    <i class="fas {{ in_array($booking->status, ['paid', 'confirmed', 'ticketed', 'completed']) ? 'fa-check' : 'fa-credit-card' }}"></i>
                                </div>
                                <div class="tl-text">
                                    <strong>{{ __('Payment Verification') }}</strong>
                                    <small>{{ in_array($booking->status, ['paid', 'confirmed', 'ticketed', 'completed']) ? __('Completed') : __('Awaiting Payment') }}</small>
                                </div>
                            </div>
                            <div class="tl-item {{ in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']) ? 'done' : '' }}">
                                <div class="tl-dot">
                                    <i class="fas {{ in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']) ? 'fa-check' : 'fa-plane' }}"></i>
                                </div>
                                <div class="tl-text">
                                    <strong>{{ __('Official Tickets Sent') }}</strong>
                                    <small>{{ in_array($booking->status, ['confirmed', 'paid', 'ticketed', 'completed']) ? __('Ready to fly') : __('Processing...') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Manage Booking / Cancellation --}}
            <div class="detail-card">
                <div class="detail-card-header" style="padding: 16px 20px;">
                    <h5><i class="fas fa-shield-alt"></i> {{ __('Manage Booking') }}</h5>
                </div>
                <div class="detail-card-body" style="padding: 20px;">
                    @if($booking->status !== 'cancelled')
                        <button class="action-btn action-btn-danger-soft mb-3" id="btn-cancel-flight" data-id="{{ $booking->id }}" onclick="openFlightCancelModal()">
                            <i class="fas fa-times-circle"></i> {{ __('Request Cancellation') }}
                        </button>
                    @endif
                    <div class="support-links">
                        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '') }}" target="_blank"><i class="fab fa-whatsapp" style="color: #25d366;"></i> {{ __('Contact Support') }}</a>
                        <a href="#"><i class="fas fa-question-circle"></i> {{ __('Help Center') }}</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="form-cancel-flight" action="{{ route('customer.bookings.flights.cancel', $booking->id) }}" method="POST" style="display:none;">@csrf</form>

{{-- Custom Flight Cancellation Modal --}}
<div id="flightCancelModal" class="cancel-modal-overlay" style="display:none;" onclick="if(event.target===this) closeFlightCancelModal();">
    <div class="cancel-modal-container" id="flightModalContainer">
        <div class="modal-top-accent" id="flightModalAccent"></div>
        <div class="cancel-modal-header">
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon-badge" id="modalHeaderBadge">
                    <i class="fas fa-plane-slash text-danger"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" id="modalTitleText">{{ __('طلب إلغاء حجز الطيران') }}</h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-light text-secondary border">{{ __('المرجع:') }} #{{ $booking->booking_reference }}</span>
                        @if($booking->airline_name)
                        <span class="badge bg-light text-secondary border">{{ $booking->airline_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeFlightCancelModal()">&times;</button>
        </div>
        <div class="cancel-modal-body">
            {{-- Loading State --}}
            <div id="flightCancelLoading" class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 2.8rem; height: 2.8rem;"></div>
                <h6 class="fw-bold mb-1">{{ __('جاري فحص شروط وتكلفة الإلغاء مع شركة الطيران...') }}</h6>
                <p class="text-muted small mb-0">{{ __('نتواصل مباشرة مع نظام الحجز العالمي للتحقق من سياسة التذكرة...') }}</p>
            </div>
            {{-- Loaded Details Content --}}
            <div id="flightCancelContent" style="display:none;"></div>
        </div>
        <div class="cancel-modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeFlightCancelModal()">{{ __('إغلاق') }}</button>
            <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '') }}?text={{ urlencode(__('مرحباً، أود الاستفسار بخصوص إلغاء حجز الطيران رقم: ') . $booking->booking_reference) }}" 
               target="_blank" class="btn-modal-support" id="btnFlightSupport" style="display:none;">
                <i class="fab fa-whatsapp me-1"></i> {{ __('تواصل مع الدعم للمساعدة') }}
            </a>
            <button type="button" class="btn-modal-confirm" id="btnConfirmFlightCancel" style="display:none;" onclick="submitFlightCancel()">
                <i class="fas fa-check-circle me-1"></i> {{ __('تأكيد إلغاء الحجز') }}
            </button>
        </div>
    </div>
</div>

@if($booking->status === 'pending')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerBox = document.getElementById('pnrTimer');
        if (!timerBox) return;
        const display = document.getElementById('timerDisplay');
        const payButton = document.getElementById('payButton');
        const expiryDate = new Date(timerBox.dataset.expiry).getTime();

        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryDate - now;

            if (distance < 0) {
                clearInterval(x);
                if (display) {
                    display.innerHTML = "00:00";
                    display.classList.add('expired');
                }
                if (payButton) {
                    payButton.classList.add('disabled');
                    payButton.style.opacity = '0.5';
                    payButton.style.pointerEvents = 'none';
                    payButton.innerHTML = '<i class="fas fa-times-circle"></i> {{ __("Expired") }}';
                }
                setTimeout(() => { window.location.reload(); }, 2000);
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (display) {
                display.innerHTML = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                if (distance < 60000) {
                    display.style.color = '#ef4444';
                }
            }
        }, 1000);
    });
</script>
@endpush
@endif

@push('scripts')
<script>
function openFlightCancelModal() {
    const modal = document.getElementById('flightCancelModal');
    const container = document.getElementById('flightModalContainer');
    const loading = document.getElementById('flightCancelLoading');
    const content = document.getElementById('flightCancelContent');
    const confirmBtn = document.getElementById('btnConfirmFlightCancel');
    const supportBtn = document.getElementById('btnFlightSupport');
    const headerBadge = document.getElementById('modalHeaderBadge');

    modal.style.display = 'flex';
    container.className = 'cancel-modal-container theme-info';
    loading.style.display = 'block';
    content.style.display = 'none';
    confirmBtn.style.display = 'none';
    confirmBtn.disabled = true;
    supportBtn.style.display = 'none';

    fetch('{{ route("customer.bookings.flights.cancel-quote", $booking->id) }}')
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';
            content.style.display = 'block';

            let isNonRefundable = (!data.refundable || parseFloat(data.refund_amount || 0) <= 0) && !data.is_pending;
            let html = '';

            if (data.is_pending) {
                container.className = 'cancel-modal-container theme-info';
                if (headerBadge) headerBadge.innerHTML = '<i class="fas fa-info-circle text-primary"></i>';
                html += `
                    <div class="mb-3">
                        <div class="info-status-badge">
                            <span class="pulse-status-dot"></span>
                            <span>{{ __('حجز غير مؤكد (PENDING)') }}</span>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-3" style="font-size:0.92rem; line-height:1.6;">
                        <i class="fas fa-info-circle fa-lg"></i>
                        <div><strong>{{ __('إلغاء فوري مجاني:') }}</strong> {{ __('لم يتم إصدار التذاكر بعد من شركة الطيران. يمكنك إلغاء الحجز فوراً وبشكل مجاني بالكامل.') }}</div>
                    </div>`;
                confirmBtn.style.display = 'inline-flex';
                confirmBtn.disabled = false;
                confirmBtn.className = 'btn-modal-confirm';
                confirmBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> {{ __("تأكيد إلغاء الحجز المجاني") }}';

            } else if (isNonRefundable) {
                container.className = 'cancel-modal-container theme-danger';
                if (headerBadge) headerBadge.innerHTML = '<i class="fas fa-shield-alt text-danger"></i>';
                html += `
                    <div class="danger-hero-card">
                        <div class="danger-status-badge">
                            <span class="pulse-status-dot"></span>
                            <span>{{ __('تذكرة غير قابلة للإلغاء أو الاسترداد (NON-REFUNDABLE)') }}</span>
                        </div>
                        <h6 class="fw-bold text-danger mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('قيود وشروط شركة الطيران الصارمة:') }}
                        </h6>
                        <p class="mb-0 text-danger-emphasis" style="font-size:0.92rem; line-height: 1.6; font-weight: 500;">
                            ${data.message || '{{ __("هذه التذكرة تتبع فئة أسعار ترويجية غير قابلة للإلغاء أو الاسترجاع المالي نهائياً وفق شروط شركة الطيران.") }}'}
                        </p>
                    </div>

                    <div class="price-breakdown-box border-danger-soft">
                        <div class="price-row">
                            <span class="text-muted">{{ __('قيمة التذكرة المدفوعة:') }}</span>
                            <span class="fw-bold">${data.penalty} ${data.currency}</span>
                        </div>
                        <div class="price-row text-danger">
                            <span>{{ __('غرامة الإلغاء المفروضة (100%):') }}</span>
                            <span class="fw-bold">-${data.penalty} ${data.currency}</span>
                        </div>
                        <div class="price-row total-row text-danger">
                            <span>{{ __('المبلغ المسترد للمحفظة / الحساب:') }}</span>
                            <span class="fw-bolder fs-5 text-danger">0.00 ${data.currency}</span>
                        </div>
                    </div>

                    <div class="protection-callout">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-lock fa-lg text-danger mt-1"></i>
                            <div>
                                <strong class="d-block mb-1 text-danger">{{ __('تم قفل الإلغاء الذاتي لحماية تذكرتك ومقعدك:') }}</strong>
                                {{ __('حرصاً على حماية حقوقك من خسارة قيمة التذكرة والمقاعد دون أي مقابل مالي، تم إيقاف الإلغاء المباشر. إذا كانت لديك حالة طارئة، يرجى التواصل مع فريق الدعم الفني لمتابعة الحالة استثنائياً مع شركة الطيران.') }}
                            </div>
                        </div>
                    </div>`;
                
                // Do NOT allow confirming cancellation!
                confirmBtn.style.display = 'none';
                supportBtn.style.display = 'inline-flex';

            } else {
                const isPartial = !!data.is_partial;
                container.className = isPartial ? 'cancel-modal-container theme-info' : 'cancel-modal-container theme-success';
                if (headerBadge) headerBadge.innerHTML = isPartial ? '<i class="fas fa-coins text-primary"></i>' : '<i class="fas fa-undo-alt text-success"></i>';

                const badgeTitle = data.badge_text || (isPartial ? '{{ __("استرداد جزئي / ضرائب ورسوم (PARTIAL TAX REFUND)") }}' : '{{ __("تذكرة قابلة للاسترداد (REFUNDABLE)") }}');
                const badgeClass = isPartial ? 'info-status-badge' : 'success-status-badge';
                const alertClass = isPartial ? 'alert-info' : 'alert-success';

                html += `
                    <div class="mb-3">
                        <div class="${badgeClass}">
                            <span class="pulse-status-dot"></span>
                            <span>${badgeTitle}</span>
                        </div>
                    </div>
                    <div class="alert ${alertClass} mb-3" style="font-size: 0.92rem; line-height: 1.6;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>{{ __('شروط الاسترداد المقررة من شركة الطيران:') }}</strong>
                        <div class="mt-1">${data.message}</div>
                    </div>
                    <div class="price-breakdown-box">
                        <div class="price-row text-danger">
                            <span>{{ __('غرامة الإلغاء المقررة:') }}</span>
                            <span class="fw-bold">-${data.penalty} ${data.currency}</span>
                        </div>
                        <div class="price-row total-row text-success">
                            <span>{{ __('المبلغ المسترد لمحفظتك في المنصة:') }}</span>
                            <span class="fw-bolder fs-5 text-success">${data.refund_amount} ${data.currency}</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 border border-success-subtle bg-success-subtle text-success small mb-0 mt-3" style="line-height: 1.6;">
                        <i class="fas fa-wallet me-1"></i>
                        {{ __('عند تأكيد الإلغاء، سيتم إلغاء التذكرة فوراً وإيداع المبلغ المسترد في محفظتك الإلكترونية بالمنصة لتتمكن من استخدامه في أي حجز آخر.') }}
                    </div>`;
                
                confirmBtn.style.display = 'inline-flex';
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> {{ __("تأكيد طلب الإلغاء والاسترداد") }}';
            }

            content.innerHTML = html;
        })
        .catch(err => {
            loading.style.display = 'none';
            content.style.display = 'block';
            content.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ __('تعذر التحقق التلقائي من سياسة شركة الطيران حالياً. يرجى التواصل مع فريق الدعم للمساعدة.') }}
                </div>`;
            confirmBtn.style.display = 'none';
            supportBtn.style.display = 'inline-flex';
        });
}

function closeFlightCancelModal() {
    const modal = document.getElementById('flightCancelModal');
    if (modal) modal.style.display = 'none';
}

function submitFlightCancel() {
    const btn = document.getElementById('btnConfirmFlightCancel');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __("جاري الإلغاء...") }}';
    document.getElementById('form-cancel-flight').submit();
}

var btnCancelFlight = document.getElementById('btn-cancel-flight');
if (btnCancelFlight) {
    btnCancelFlight.addEventListener('click', function(e) {
        e.preventDefault();
        openFlightCancelModal();
    });
}
</script>
@endpush
@endsection
