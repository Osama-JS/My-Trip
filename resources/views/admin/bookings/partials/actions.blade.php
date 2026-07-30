<div class="d-flex align-items-center gap-1">
    <a href="{{ route($show_route ?? 'admin.bookings.show', $id) }}"
       class="act-action-btn" title="{{ __('View Details') }}">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route($invoice_route ?? 'admin.bookings.invoice', $id) }}"
       target="_blank"
       class="act-action-btn act-action-btn--gold" title="{{ __('Invoice') }}">
        <i class="fas fa-file-invoice"></i>
    </a>
</div>
