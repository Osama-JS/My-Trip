@extends('frontend.customer.layouts.customer-layout')

@section('title', __('Travel Insurances'))
@section('page-title', __('Travel Insurances & Protection'))

@section('content')
<style>
    .insurance-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .insurance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.07);
    }
    .insurance-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 16px;
        margin-bottom: 16px;
    }
    .insurance-shield {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #fff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .insurance-badge-active {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }
</style>

<div class="cdash-content cdash-animate">
    <!-- Header Summary Card -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="font-w800 mb-1" style="font-size: 1.6rem; color: var(--text-main);">
                <i class="fas fa-shield-alt text-primary me-2"></i>{{ __('My Travel Insurance Certificates') }}
            </h2>
            <p class="text-muted mb-0">{{ __('Official international insurance certificates compliant with Schengen visa and worldwide travel standards.') }}</p>
        </div>
    </div>

    <!-- Policies List -->
    @forelse($policies as $policy)
        <div class="insurance-card">
            <div class="insurance-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="insurance-shield">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 font-w800" style="color: var(--text-main);">{{ $policy->policy_number }}</h4>
                            <span class="insurance-badge-active">✓ {{ strtoupper($policy->status) }}</span>
                        </div>
                        <small class="text-muted">
                            {{ __('Certificate') }}: {{ $policy->certificate_number ?: '-' }} • {{ __('Issued') }}: {{ $policy->created_at->format('d M Y') }}
                        </small>
                    </div>
                </div>
                <div>
                    <a href="{{ route('customer.insurances.pdf', $policy->id) }}" class="btn btn-primary btn-sm px-3 py-2 rounded-pill font-w700" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none;">
                        <i class="fas fa-file-pdf me-2"></i>{{ __('Download Official Certificate (PDF)') }}
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <small class="text-muted d-block">{{ __('Coverage Plan') }}</small>
                    <strong style="color: var(--text-main);">{{ ucfirst($policy->coverage_type) }} Travel Safe</strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <small class="text-muted d-block">{{ __('Destination') }}</small>
                    <strong style="color: var(--text-main);"><i class="fas fa-globe me-1 text-primary"></i>{{ $policy->destination_country_name }}</strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <small class="text-muted d-block">{{ __('Period of Cover') }}</small>
                    <strong style="color: var(--text-main);">{{ $policy->departure_date ? $policy->departure_date->format('d M Y') : '-' }} → {{ $policy->return_date ? $policy->return_date->format('d M Y') : '-' }}</strong>
                </div>
                <div class="col-sm-6 col-md-3">
                    <small class="text-muted d-block">{{ __('Duration') }}</small>
                    <strong style="color: var(--text-main);">{{ $policy->duration_days }} {{ __('Days') }}</strong>
                </div>
            </div>

            <hr style="border-color: var(--border-color); margin: 16px 0;">

            <!-- Insured Travelers Preview -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <small class="text-muted d-block mb-1">{{ __('Insured Persons') }}:</small>
                    <div class="d-flex flex-wrap gap-2">
                        @php $travelers = $policy->insured_passengers ?? []; @endphp
                        @forelse($travelers as $t)
                            <span class="badge bg-light text-dark border p-2">
                                <i class="fas fa-user text-primary me-1"></i>
                                {{ strtoupper($t['first_name'] ?? ($t['name'] ?? '')) }} {{ strtoupper($t['last_name'] ?? '') }}
                                <span class="text-muted">({{ strtoupper($t['passport_no'] ?? ($t['passport'] ?? '')) }})</span>
                            </span>
                        @empty
                            <span class="badge bg-light text-dark border">{{ auth()->user()->name }}</span>
                        @endforelse
                    </div>
                </div>
                <div class="text-end">
                    <small class="text-danger font-w700 d-block"><i class="fas fa-ambulance me-1"></i>{{ __('24/7 Medical Emergency Assistance') }}:</small>
                    <span class="font-w800 text-dark dir-ltr" style="font-size: 1.05rem;">{{ $policy->emergency_phone ?: '+1-800-456-7890' }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5 bg-card rounded-4 border p-5">
            <i class="fas fa-shield-alt fa-3x text-muted opacity-50 mb-3"></i>
            <h4 class="font-w700 text-dark">{{ __('No Active Insurance Certificates') }}</h4>
            <p class="text-muted mb-4">{{ __('When you add travel insurance protection during booking flight or trip, your certificate will appear here.') }}</p>
            <a href="{{ route('flights') }}" class="btn btn-primary rounded-pill px-4">{{ __('Book a Flight') }}</a>
        </div>
    @endforelse

    @if($policies->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $policies->links() }}
        </div>
    @endif
</div>
@endsection
