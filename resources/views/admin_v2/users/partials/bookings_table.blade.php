<div class="table-responsive">
    <table class="table-v2 v2-table w-100" style="margin-bottom: 0;">
        <thead>
            @if($type === 'flights')
                <tr>
                    <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز / PNR' : 'Ref / PNR' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'شركة الطيران' : 'Airline' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'مسار الرحلة' : 'Flight Route' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تاريخ السفر' : 'Travel Date' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Booked At' }}</th>
                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</th>
                </tr>
            @elseif($type === 'hotels')
                <tr>
                    <th>{{ app()->getLocale() == 'ar' ? 'رقم المرجع' : 'Reference #' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الفندق والمدينة' : 'Hotel & City' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الغرف والنزلاء' : 'Rooms & Guests' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تواريخ الإقامة' : 'Stay Dates' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Booked At' }}</th>
                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</th>
                </tr>
            @elseif($type === 'trips')
                <tr>
                    <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Booking #' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الباقة / الرحلة' : 'Package / Trip' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'التذاكر' : 'Tickets' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'موعد الرحلة' : 'Trip Date' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Booked At' }}</th>
                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</th>
                </tr>
            @else
                {{-- 'all' --}}
                <tr>
                    <th>{{ app()->getLocale() == 'ar' ? 'رقم الحجز' : 'Ref #' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الخدمة' : 'Service' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تفاصيل الحجز' : 'Booking Details' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'المبلغ' : 'Amount' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'تاريخ الحجز' : 'Booked At' }}</th>
                    <th class="text-center">{{ app()->getLocale() == 'ar' ? 'عرض' : 'View' }}</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($bookings as $b)
                @php
                    $rawStatus = strtolower($b->status ?? '');
                    $isConfirmed = in_array($rawStatus, ['confirmed', 'paid', 'completed', 'active']);
                    $isPending = in_array($rawStatus, ['pending', 'processing', 'awaiting_payment']);
                @endphp

                @if($type === 'flights')
                    @php
                        $fb = $b->flightBooking;
                        $depDate = $fb && $fb->departure_date ? \Carbon\Carbon::parse($fb->departure_date)->format('Y-m-d') : null;
                        $retDate = $fb && $fb->return_date ? \Carbon\Carbon::parse($fb->return_date)->format('Y-m-d') : null;
                    @endphp
                    <tr>
                        <td>
                            <strong style="font-family: monospace; color: var(--primary); font-size: 0.9rem;">
                                #{{ $b->booking_reference ?: $b->id }}
                            </strong>
                            @if($b->pnr_code)
                                <small class="text-muted d-block" style="font-family: monospace; font-size: 0.75rem;">PNR: {{ $b->pnr_code }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark fw-bold border" style="font-size: 0.75rem;">
                                    {{ $b->airline_code ?: 'FL' }}
                                </span>
                                <span style="font-weight: 700; color: var(--text-main);">
                                    {{ $b->airline_name ?: __('Flight') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($fb && ($fb->origin || $fb->destination))
                                <div class="d-flex align-items-center gap-1">
                                    <strong style="font-family: monospace; color: var(--text-main);">{{ $fb->origin ?: '---' }}</strong>
                                    <i class="fa-solid fa-plane-departure text-primary mx-1" style="font-size: 0.75rem;"></i>
                                    <strong style="font-family: monospace; color: var(--text-main);">{{ $fb->destination ?: '---' }}</strong>
                                </div>
                                @if($fb->flight_number)
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $fb->flight_number }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($depDate)
                                <div style="font-weight: 600; color: var(--text-main);">{{ $depDate }}</div>
                                @if($retDate)
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-rotate-left me-1"></i> {{ $retDate }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--text-main); font-size: 0.9rem;">
                                {{ number_format($b->total_amount, 2) }} <small style="font-size: 0.75rem;">SAR</small>
                            </strong>
                        </td>
                        <td>
                            @if($isConfirmed)
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ $b->status }}</span>
                            @elseif($isPending)
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i> {{ $b->status }}</span>
                            @else
                                <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark me-1"></i> {{ $b->status }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">{{ $b->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.bookings.flights.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                <i class="fa-solid fa-eye text-primary"></i>
                            </a>
                        </td>
                    </tr>

                @elseif($type === 'hotels')
                    <tr>
                        <td>
                            <strong style="font-family: monospace; color: var(--success, #10b981); font-size: 0.9rem;">
                                #{{ $b->reference_num ?: $b->id }}
                            </strong>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $b->hotel_name ?: __('Hotel Booking') }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $b->city_name ?: '—' }} {{ $b->country_name ? ', '.$b->country_name : '' }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border me-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-bed me-1"></i> {{ $b->rooms ?: 1 }} {{ app()->getLocale() == 'ar' ? 'غرف' : 'Rooms' }}
                            </span>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ $b->adults ?: 1 }} {{ app()->getLocale() == 'ar' ? 'بالغين' : 'Adults' }}
                            </small>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-main);">
                                {{ $b->check_in ? $b->check_in->format('Y-m-d') : '—' }} ➔ {{ $b->check_out ? $b->check_out->format('Y-m-d') : '—' }}
                            </div>
                            @if($b->check_in && $b->check_out)
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    ({{ $b->check_in->diffInDays($b->check_out) }} {{ app()->getLocale() == 'ar' ? 'ليالٍ' : 'Nights' }})
                                </small>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--text-main); font-size: 0.9rem;">
                                {{ number_format($b->total_price, 2) }} <small style="font-size: 0.75rem;">SAR</small>
                            </strong>
                        </td>
                        <td>
                            @if($isConfirmed)
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ $b->status }}</span>
                            @elseif($isPending)
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i> {{ $b->status }}</span>
                            @else
                                <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark me-1"></i> {{ $b->status }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">{{ $b->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.bookings.hotels.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                <i class="fa-solid fa-eye text-success"></i>
                            </a>
                        </td>
                    </tr>

                @elseif($type === 'trips')
                    <tr>
                        <td>
                            <strong style="font-family: monospace; color: var(--warning, #f59e0b); font-size: 0.9rem;">
                                #{{ $b->id }}
                            </strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $b->trip && $b->trip->image_url ? $b->trip->image_url : asset('images/no-image.jpg') }}" 
                                     width="36" height="36" class="rounded object-fit-cover border" alt="">
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 0.85rem;">
                                        {{ $b->trip->title ?? __('Tour Package') }}
                                    </div>
                                    @if($b->trip && $b->trip->destination)
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-map-pin me-1"></i> {{ $b->trip->destination }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-ticket me-1"></i> {{ $b->tickets_count ?: 1 }} {{ app()->getLocale() == 'ar' ? 'تذاكر' : 'Tickets' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-main);">
                                {{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d') : '—' }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: var(--text-main); font-size: 0.9rem;">
                                {{ number_format($b->total_price, 2) }} <small style="font-size: 0.75rem;">SAR</small>
                            </strong>
                        </td>
                        <td>
                            @if($isConfirmed)
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ $b->status }}</span>
                            @elseif($isPending)
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i> {{ $b->status }}</span>
                            @else
                                <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark me-1"></i> {{ $b->status }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">{{ $b->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.trip-bookings.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                <i class="fa-solid fa-eye text-warning"></i>
                            </a>
                        </td>
                    </tr>

                @else
                    {{-- 'all' --}}
                    @php
                        $bType = $b->booking_type ?? (isset($b->hotel_name) ? 'hotel' : (($b->type ?? '') === 'flight' || isset($b->flight_booking_id) || isset($b->airline_name) ? 'flight' : 'trip'));
                    @endphp
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: 700; color: var(--primary);">
                                #{{ $b->booking_reference ?? $b->reference_num ?? $b->id }}
                            </span>
                        </td>
                        <td>
                            @if($bType === 'flight')
                                <span class="badge-v2 badge-primary"><i class="fa-solid fa-plane"></i> {{ app()->getLocale() == 'ar' ? 'طيران' : 'Flight' }}</span>
                            @elseif($bType === 'hotel')
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-hotel"></i> {{ app()->getLocale() == 'ar' ? 'فندق' : 'Hotel' }}</span>
                            @else
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-suitcase"></i> {{ app()->getLocale() == 'ar' ? 'باقة' : 'Tour' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($bType === 'flight')
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.85rem;">{{ $b->airline_name ?: __('Flight Booking') }}</strong>
                                    @if(isset($b->flightBooking) && ($b->flightBooking->origin || $b->flightBooking->destination))
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            {{ $b->flightBooking->origin }} ➔ {{ $b->flightBooking->destination }}
                                        </small>
                                    @endif
                                </div>
                            @elseif($bType === 'hotel')
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.85rem;">{{ $b->hotel_name ?: __('Hotel Booking') }}</strong>
                                    @if($b->city_name)
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-location-dot me-1"></i> {{ $b->city_name }}
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.85rem;">{{ $b->trip->title ?? __('Tour Package') }}</strong>
                                    @if(isset($b->trip) && $b->trip->destination)
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-map-pin me-1"></i> {{ $b->trip->destination }}
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--text-main); font-size: 0.9rem;">
                                {{ number_format($b->total_amount ?? $b->total_price ?? 0, 2) }} <small style="font-size: 0.75rem;">SAR</small>
                            </strong>
                        </td>
                        <td>
                            @if($isConfirmed)
                                <span class="badge-v2 badge-success"><i class="fa-solid fa-circle-check me-1"></i> {{ $b->status }}</span>
                            @elseif($isPending)
                                <span class="badge-v2 badge-warning"><i class="fa-solid fa-clock me-1"></i> {{ $b->status }}</span>
                            @else
                                <span class="badge-v2 badge-danger"><i class="fa-solid fa-circle-xmark me-1"></i> {{ $b->status }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">{{ $b->created_at->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="text-center">
                            @if($bType === 'flight')
                                <a href="{{ route('admin.bookings.flights.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                    <i class="fa-solid fa-eye text-primary"></i>
                                </a>
                            @elseif($bType === 'hotel')
                                <a href="{{ route('admin.bookings.hotels.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                    <i class="fa-solid fa-eye text-success"></i>
                                </a>
                            @else
                                <a href="{{ route('admin.trip-bookings.show', $b->id) }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'عرض تفاصيل الحجز' : 'View Booking' }}">
                                    <i class="fa-solid fa-eye text-warning"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="color: var(--text-muted);">
                            <i class="fa-solid fa-calendar-xmark mb-3" style="font-size: 2.5rem; opacity: 0.35;"></i>
                            <div style="font-weight: 700; font-size: 1rem; color: var(--text-main);">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد حجوزات مطابقة للبحث أو الفلتر المحدد' : 'No bookings found matching your criteria' }}
                            </div>
                            <p style="font-size: 0.85rem; margin-top: 0.25rem;">
                                {{ app()->getLocale() == 'ar' ? 'جرب تغيير خيارات التصفية أو مسح حقل البحث' : 'Try clearing filters or search query' }}
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Professional Pagination Footer --}}
<div class="v2-table-footer">
    <div class="v2-pagination-info" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">
        @if($bookings->total() > 0)
            {{ app()->getLocale() == 'ar' ? 'عرض' : 'Showing' }} 
            <strong>{{ $bookings->firstItem() ?? 0 }}</strong> 
            {{ app()->getLocale() == 'ar' ? 'إلى' : 'to' }} 
            <strong>{{ $bookings->lastItem() ?? 0 }}</strong> 
            {{ app()->getLocale() == 'ar' ? 'من أصل' : 'of' }} 
            <strong>{{ $bookings->total() }}</strong> 
            {{ app()->getLocale() == 'ar' ? 'سجل' : 'records' }}
        @else
            {{ app()->getLocale() == 'ar' ? 'لا توجد سجلات' : 'No records found' }}
        @endif
    </div>

    @if($bookings->hasPages())
        <div class="v2-pagination-nav">
            {{-- First Page --}}
            <button type="button" class="v2-page-btn booking-page-btn" data-page="1" {{ $bookings->onFirstPage() ? 'disabled' : '' }} title="{{ app()->getLocale() == 'ar' ? 'الصفحة الأولى' : 'First Page' }}">
                <i class="fa-solid fa-angles-right rtl:rotate-0"></i>
            </button>

            {{-- Previous Page --}}
            <button type="button" class="v2-page-btn booking-page-btn" data-page="{{ $bookings->currentPage() - 1 }}" {{ $bookings->onFirstPage() ? 'disabled' : '' }} title="{{ app()->getLocale() == 'ar' ? 'السابق' : 'Previous' }}">
                <i class="fa-solid fa-angle-right rtl:rotate-0"></i>
            </button>

            {{-- Numeric Page Buttons --}}
            @php
                $current = $bookings->currentPage();
                $last = $bookings->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            @if($start > 1)
                <button type="button" class="v2-page-btn booking-page-btn" data-page="1">1</button>
                @if($start > 2)
                    <span class="px-1 text-muted">...</span>
                @endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                <button type="button" class="v2-page-btn booking-page-btn {{ $p === $current ? 'active' : '' }}" data-page="{{ $p }}">
                    {{ $p }}
                </button>
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-1 text-muted">...</span>
                @endif
                <button type="button" class="v2-page-btn booking-page-btn" data-page="{{ $last }}">{{ $last }}</button>
            @endif

            {{-- Next Page --}}
            <button type="button" class="v2-page-btn booking-page-btn" data-page="{{ $bookings->currentPage() + 1 }}" {{ !$bookings->hasMorePages() ? 'disabled' : '' }} title="{{ app()->getLocale() == 'ar' ? 'التالي' : 'Next' }}">
                <i class="fa-solid fa-angle-left rtl:rotate-0"></i>
            </button>

            {{-- Last Page --}}
            <button type="button" class="v2-page-btn booking-page-btn" data-page="{{ $bookings->lastPage() }}" {{ !$bookings->hasMorePages() ? 'disabled' : '' }} title="{{ app()->getLocale() == 'ar' ? 'الصفحة الأخيرة' : 'Last Page' }}">
                <i class="fa-solid fa-angles-left rtl:rotate-0"></i>
            </button>
        </div>
    @endif
</div>
