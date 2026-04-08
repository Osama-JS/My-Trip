<div class="dropdown">
    <button type="button" class="btn btn-primary light btn-xs sharp" data-bs-toggle="dropdown">
        <svg width="20px" height="20px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route($show_route ?? 'admin.bookings.show', $id) }}">{{ __('View Details') }}</a>
        <a class="dropdown-item" href="{{ route($invoice_route ?? 'admin.bookings.invoice', $id) }}" target="_blank">{{ __('Invoice') }}</a>
    </div>

</div>
