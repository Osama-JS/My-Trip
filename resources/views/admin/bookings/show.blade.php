@extends('layouts.app')

@section('title', __('Booking Details'))

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{ __('Bookings') }}</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $booking->booking_reference }}</a></li>
    </ol>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Booking Info -->
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">{{ __('Flight Booking Details') }}</h4>
                @if($booking->ticket_status === 'ticketed')
                    <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-file-invoice"></i> {{ __('Download Invoice') }}</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-5">
                        <h6 class="text-muted">{{ __('Booking Reference') }}</h6>
                        <h3>{{ $booking->booking_reference }}</h3>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex flex-wrap gap-3 mt-3 mt-md-0 justify-content-md-end">
                            <div class="border rounded px-3 py-2 text-center">
                                <span class="d-block text-muted small fw-bold mb-1">{{ __('Booking Status') }}</span>
                                @if($booking->status == 'confirmed')
                                    <span class="badge badge-success">{{ __('Confirmed') }}</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                @elseif($booking->status == 'paid')
                                    <span class="badge badge-info">{{ __('Paid') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __(ucfirst($booking->status)) }}</span>
                                @endif
                            </div>
                            <div class="border rounded px-3 py-2 text-center">
                                <span class="d-block text-muted small fw-bold mb-1">{{ __('Payment Status') }}</span>
                                @if($booking->status == 'confirmed' || $booking->status == 'paid')
                                    <span class="badge badge-success">{{ __('Paid / Confirmed') }}</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __(ucfirst($booking->status)) }}</span>
                                @endif
                            </div>
                            <div class="border rounded px-3 py-2 text-center">
                                <span class="d-block text-muted small fw-bold mb-1">{{ __('Ticketing Status') }}</span>
                                @if($booking->ticket_status == 'ticketed')
                                    <span class="badge badge-success">{{ __('Ticketed') }}</span>
                                @elseif($booking->ticket_status == 'booked')
                                    <span class="badge badge-primary">{{ __('Booked') }}</span>
                                @elseif($booking->ticket_status == 'cancelled')
                                    <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                @else
                                    <span class="badge badge-outline-dark">{{ __(ucfirst($booking->ticket_status)) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Passengers -->
                <h5 class="mb-3 mt-4">{{ __('Passengers') }}</h5>
                <div class="row">
                    @forelse($booking->passengers as $index => $pax)
                    <div class="col-lg-12 mb-3">
                        <div class="card border shadow-none mb-0">
                            <div class="card-header border-0 pb-0">
                                <h5 class="card-title">{{ __($pax->title) }} {{ $pax->first_name }} {{ $pax->last_name }} <span class="badge badge-primary light ml-2">{{ __(ucfirst($pax->passenger_type ?? $pax->type ?? 'Passenger')) }}</span></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Document No') }}</h6>
                                                <p class="mb-0">{{ $pax->passport_number ?? $pax->passport_no ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Expiry') }}</h6>
                                                <p class="mb-0">{{ $pax->passport_expiry ? \Carbon\Carbon::parse($pax->passport_expiry)->format('d M Y') : 'N/A' }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Nationality') }}</h6>
                                                <p class="mb-0">{{ $pax->nationality ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Issue Country') }}</h6>
                                                <p class="mb-0">{{ $pax->passport_issue_country ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('DOB') }}</h6>
                                                <p class="mb-0">{{ $pax->dob ? \Carbon\Carbon::parse($pax->dob)->format('d M Y') : 'N/A' }}</p>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Phone') }}</h6>
                                                <p class="mb-0" dir="ltr">{{ $pax->phone ?? 'N/A' }}</p>
                                            </div>
                                            @if($booking->ticket_status == 'ticketed' && !empty($booking->ticket_numbers) && isset($booking->ticket_numbers[$index]))
                                            <div class="col-sm-6 mb-3">
                                                <h6 class="text-muted">{{ __('Ticket No') }}</h6>
                                                <p class="mb-0 font-w600 text-success">{{ $booking->ticket_numbers[$index] }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h6 class="text-muted mb-2">{{ __('Document Image') }}</h6>
                                        @if($pax->passport_image)
                                            @php 
                                                $rawPath = ltrim($pax->passport_image, '/');
                                                $imageUrl = str_starts_with($rawPath, 'storage/') ? asset($rawPath) : asset('storage/' . $rawPath);
                                            @endphp
                                            <a href="javascript:void(0);" onclick="openImageModal('{{ $imageUrl }}')">
                                                <img src="{{ $imageUrl }}" alt="Document" class="img-thumbnail" style="max-height: 150px; cursor: pointer;">
                                            </a>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-light border" style="height: 150px; border-radius: 5px;">
                                                <span class="text-muted">{{ __('No Image') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">{{ __('No passengers found.') }}</div>
                    @endforelse
                </div>

                <!-- Image Modal -->
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Document Preview') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="previewImageSrc" src="" alt="{{ __('Document Preview') }}" class="img-fluid rounded" style="max-height: 70vh;">
                            </div>
                        </div>
                    </div>
                </div>

                 <hr>

                  <!-- Contact Info -->
                  <h5 class="mb-3 mt-4">{{ __('Contact Information') }}</h5>
                  <div class="row mb-4">
                      <div class="col-md-6">
                          <strong>{{ __('Email') }}:</strong> {{ $booking->contact_email }}
                      </div>
                      <div class="col-md-6">
                          <strong>{{ __('Phone') }}:</strong> <span dir="ltr">{{ $booking->contact_phone }}</span>
                      </div>
                  </div>

                  <hr>

                  <!-- API Logs -->
                  <h5 class="mb-3 mt-4">{{ __('API Transaction Logs') }}</h5>
                  <div class="table-responsive">
                      <table class="table table-sm table-hover">
                          <thead>
                              <tr>
                                  <th>{{ __('Action') }}</th>
                                  <th>{{ __('Status') }}</th>
                                  <th>{{ __('Time') }}</th>
                                  <th>{{ __('Details') }}</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse($booking->flightApiLogs as $log)
                              <tr>
                                  <td>{{ $log->action }}</td>
                                  <td>
                                      <span class="badge badge-{{ $log->status_code >= 200 && $log->status_code < 300 ? 'success' : 'danger' }}">
                                          {{ $log->status_code }}
                                      </span>
                                  </td>
                                  <td>{{ $log->created_at->format('H:i:s') }}</td>
                                  <td>
                                      <button type="button" class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                          <i class="fa fa-info-circle"></i>
                                      </button>

                                      <!-- Modal -->
                                      <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg">
                                              <div class="modal-content">
                                                  <div class="modal-header">
                                                      <h5 class="modal-title">{{ __('Log Detail') }}: {{ $log->action }}</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <h6>{{ __('Request Payload') }}</h6>
                                                      <pre class="bg-light p-2"><code>{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                      <h6 class="mt-3">{{ __('Response Payload') }}</h6>
                                                      <pre class="bg-light p-2"><code>{{ json_encode($log->response_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="4" class="text-center text-muted">{{ __('No API logs found for this booking.') }}</td>
                              </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>

             </div>
         </div>
     </div>

    <!-- Sidebar Info -->
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Payment Summary') }}</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Base Amount') }}</span>
                    <strong>{{ $booking->total_amount }} {{ $booking->currency }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('Tax') }}</span>
                    <strong>0.00 {{ $booking->currency }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-primary font-w600">{{ __('Total') }}</span>
                    <span class="text-primary font-w600">{{ $booking->total_amount }} {{ $booking->currency }}</span>
                </div>
            </div>
            <div class="card-footer text-center">
                 <small class="text-muted">{{ __('Created') }}: {{ $booking->created_at->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openImageModal(imgUrl) {
        $('#previewImageSrc').attr('src', imgUrl);
        var imageModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        imageModal.show();
    }
</script>
@endpush
