@extends('layouts.app')

@section('title', 'Booking Details')

@section('page-header')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
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
                <h4 class="card-title">Flight Booking Details</h4>
                @if($booking->ticket_status === 'ticketed')
                    <a href="{{ route('admin.bookings.invoice', $booking->id) }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-file-invoice"></i> Download Invoice</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Booking Reference</h6>
                        <h3>{{ $booking->booking_reference }}</h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="mb-2">
                            <h6 class="text-muted mb-1">Payment Status</h6>
                            @if($booking->status == 'confirmed' || $booking->status == 'paid')
                                <span class="badge badge-success">Paid / Confirmed</span>
                            @elseif($booking->status == 'cancelled')
                                <span class="badge badge-danger">Cancelled</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Ticketing Status</h6>
                            @if($booking->ticket_status == 'ticketed')
                                <span class="badge badge-success">Ticketed</span>
                            @elseif($booking->ticket_status == 'booked')
                                <span class="badge badge-primary">Booked</span>
                            @elseif($booking->ticket_status == 'cancelled')
                                <span class="badge badge-danger">Cancelled</span>
                            @else
                                <span class="badge badge-outline-dark">{{ ucfirst($booking->ticket_status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Passengers -->
                <h5 class="mb-3 mt-4">Passengers</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Passport No</th>
                                <th>Nationality</th>
                                <th>DOB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->passengers as $pax)
                            <tr>
                                <td>{{ ucfirst($pax->type) }}</td>
                                <td>{{ $pax->title }} {{ $pax->first_name }} {{ $pax->last_name }}</td>
                                <td>{{ $pax->passport_no ?? '-' }}</td>
                                <td>{{ $pax->nationality }}</td>
                                <td>{{ $pax->dob ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                 <hr>

                 <!-- Contact Info -->
                  <h5 class="mb-3 mt-4">Contact Information</h5>
                  <div class="row mb-4">
                      <div class="col-md-6">
                          <strong>Email:</strong> {{ $booking->contact_email }}
                      </div>
                      <div class="col-md-6">
                          <strong>Phone:</strong> {{ $booking->contact_phone }}
                      </div>
                  </div>

                  <hr>

                  <!-- API Logs -->
                  <h5 class="mb-3 mt-4">API Transaction Logs</h5>
                  <div class="table-responsive">
                      <table class="table table-sm table-hover">
                          <thead>
                              <tr>
                                  <th>Action</th>
                                  <th>Status</th>
                                  <th>Time</th>
                                  <th>Details</th>
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
                                                      <h5 class="modal-title">Log Detail: {{ $log->action }}</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                  </div>
                                                  <div class="modal-body">
                                                      <h6>Request Payload</h6>
                                                      <pre class="bg-light p-2"><code>{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                      <h6 class="mt-3">Response Payload</h6>
                                                      <pre class="bg-light p-2"><code>{{ json_encode($log->response_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="4" class="text-center text-muted">No API logs found for this booking.</td>
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
                <h4 class="card-title">Payment Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Base Amount</span>
                    <strong>{{ $booking->total_amount }} {{ $booking->currency }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <strong>0.00 {{ $booking->currency }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-primary font-w600">Total</span>
                    <span class="text-primary font-w600">{{ $booking->total_amount }} {{ $booking->currency }}</span>
                </div>
            </div>
            <div class="card-footer text-center">
                 <small class="text-muted">Created: {{ $booking->created_at->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
