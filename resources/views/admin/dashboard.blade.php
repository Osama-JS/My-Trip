@extends('layouts.app')

@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card booking">
                    <div class="card-body">
                        <div class="booking-status d-flex align-items-center">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                                  <path data-name="Path 1957" d="M129.035,178.842v2.8a5.6,5.6,0,0,0,5.6,5.6h14a5.6,5.6,0,0,0,5.6-5.6v-16.8a5.6,5.6,0,0,0-5.6-5.6h-14a5.6,5.6,0,0,0-5.6,5.6v2.8a1.4,1.4,0,0,0,2.8,0v-2.8a2.8,2.8,0,0,1,2.8-2.8h14a2.8,2.8,0,0,1,2.8,2.8v16.8a2.8,2.8,0,0,1-2.8,2.8h-14a2.8,2.8,0,0,1-2.8-2.8v-2.8a1.4,1.4,0,0,0-2.8,0Zm10.62-7-1.81-1.809a1.4,1.4,0,1,1,1.98-1.981l4.2,4.2a1.4,1.4,0,0,1,0,1.981l-4.2,4.2a1.4,1.4,0,1,1-1.98-1.981l1.81-1.81h-12.02a1.4,1.4,0,1,1,0-2.8Z" transform="translate(-126.235 -159.242)" fill="var(--primary)" fill-rule="evenodd"/>
                                </svg>
                            </span>
                            <div class="ms-4">
                                <h2 class="mb-0 font-w600">{{ \App\Models\User::count() }}</h2>
                                <p class="mb-0 text-nowrap">{{ __('Total Users') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card booking">
                    <div class="card-body">
                        <div class="booking-status d-flex align-items-center">
                            <span>
                                <i class="flaticon-381-settings-1 fs-30 text-primary"></i>
                            </span>
                            <div class="ms-4">
                                <h2 class="mb-0 font-w600">{{ \Spatie\Permission\Models\Role::count() }}</h2>
                                <p class="mb-0 text-nowrap">{{ __('Active Roles') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card booking">
                    <div class="card-body">
                        <div class="booking-status d-flex align-items-center">
                            <span>
                                <i class="flaticon-381-lock-1 fs-30 text-primary"></i>
                            </span>
                            <div class="ms-4">
                                <h2 class="mb-0 font-w600">{{ \Spatie\Permission\Models\Permission::count() }}</h2>
                                <p class="mb-0 text-nowrap">{{ __('Permissions') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card booking">
                    <div class="card-body">
                        <div class="booking-status d-flex align-items-center">
                            <span>
                                <i class="flaticon-381-briefcase fs-30 text-primary"></i>
                            </span>
                            <div class="ms-4">
                                <h2 class="mb-0 font-w600">0</h2>
                                <p class="mb-0 text-nowrap">{{ __('New Bookings') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20">{{ __('System Quick View') }}</h4>
                    </div>
                    <div class="card-body">
                        <p>{{ __('Welcome to My Trip Admin Dashboard. Use the sidebar to manage users, roles, and permissions.') }}</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ __('Your Profile') }}</h5>
                                <p>{{ __('Name') }}: {{ auth()->user()->name }}</p>
                                <p>{{ __('Email') }}: {{ auth()->user()->email }}</p>
                                <p>{{ __('Type') }}: <span class="badge badge-primary">{{ auth()->user()->user_type }}</span></p>
                            </div>
                            <div class="col-md-6 text-center">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="rounded-circle" width="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
