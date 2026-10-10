@extends('admin_v2.layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'مسار وبرنامج الرحلة' : 'Trip Itinerary') . ' : ' . ($trip->title_ar ?? $trip->title_en ?? $trip->title))

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.trips.index') }}" class="btn-icon" title="{{ app()->getLocale() == 'ar' ? 'رجوع للرحلات' : 'Back to Trips' }}">
                <i class="fa-solid fa-arrow-right rtl:rotate-0"></i>
            </a>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem;">
                    {{ app()->getLocale() == 'ar' ? 'جدول ومسار الرحلة اليومي' : 'Trip Schedule & Itinerary' }}: {{ $trip->title_ar ?? $trip->title_en ?? $trip->title }}
                </h1>
                <span style="color: var(--text-muted); font-size: 0.8rem;">
                    {{ app()->getLocale() == 'ar' ? 'إدارة الأيام، الأنشطة، وإعادة الترتيب بالسحب والإفلات.' : 'Manage daily activities, excursions, and drag & drop reordering.' }}
                </span>
            </div>
        </div>

        <div style="display: flex; gap: 0.6rem;">
            <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-secondary" style="border-radius: var(--radius-md); font-weight: 700;">
                <i class="fa-solid fa-pen-to-square me-1"></i> {{ app()->getLocale() == 'ar' ? 'تعديل بيانات البرنامج' : 'Edit Package' }}
            </a>
        </div>
    </div>

    <!-- Trip Overview Card -->
    <div class="admin-card p-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-3 text-center border-end">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(59,130,246,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.5rem;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h6 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">{{ $trip->company->name ?? 'N/A' }}</h6>
                @if($trip->is_ad)
                    <span class="badge bg-warning text-dark">{{ app()->getLocale() == 'ar' ? 'إعلان مروج' : 'SPONSORED' }}</span>
                @else
                    <span class="badge {{ $trip->is_public ? 'bg-success' : 'bg-secondary' }}">
                        {{ $trip->is_public ? (app()->getLocale() == 'ar' ? 'عام' : 'Public') : (app()->getLocale() == 'ar' ? 'خاص' : 'Private') }}
                    </span>
                @endif
            </div>

            <div class="col-md-5 border-end px-4">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="text-center">
                        <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'من' : 'From' }}</small>
                        <strong style="color: var(--text-main);">{{ $trip->fromCity->name ?? 'N/A' }}</strong>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $trip->fromCountry->name ?? '' }}</small>
                    </div>
                    <div style="flex-grow: 1; margin: 0 1rem; position: relative; text-align: center;">
                        <hr style="border-top: 2px dashed var(--border-light); margin: 0.75rem 0 0;">
                        <i class="fa-solid fa-plane text-primary" style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); background: var(--bg-card); padding: 0 0.5rem;"></i>
                    </div>
                    <div class="text-center">
                        <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'إلى' : 'To' }}</small>
                        <strong style="color: var(--text-main);">{{ $trip->toCity->name ?? 'N/A' }}</strong>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $trip->toCountry->name ?? '' }}</small>
                    </div>
                </div>

                <div class="row text-center g-2 pt-2 border-top">
                    <div class="col-4">
                        <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'المدة' : 'Duration' }}</small>
                        <strong>{{ $trip->duration }}</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'التذاكر' : 'Tickets' }}</small>
                        <strong>{{ $trip->tickets }}</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'السعة' : 'Capacity' }}</small>
                        <strong>{{ $trip->personnel_capacity }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-md-4 ps-md-4 text-center text-md-start">
                <div class="mb-3">
                    <small class="text-muted d-block">{{ app()->getLocale() == 'ar' ? 'السعر الإجمالي' : 'Total Price' }}</small>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--primary);">
                        ${{ number_format($trip->price, 2) }}
                        @if($trip->price_before_discount > $trip->price)
                            <span class="text-muted text-decoration-line-through" style="font-size: 1rem; font-weight: 500;">
                                ${{ number_format($trip->price_before_discount, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.3rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'الربح المتوقع:' : 'Profit:' }}</span>
                        <strong class="text-success">+${{ number_format($trip->profit, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'هامش الربح:' : 'Margin:' }}</span>
                        <span class="badge bg-info text-white">{{ $trip->percentage_profit_margin }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Add Itinerary Form -->
        <div class="col-lg-4">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-plus-circle text-primary"></i>
                    <h5 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        {{ app()->getLocale() == 'ar' ? 'إضافة يوم جديد للمسار' : 'Add Itinerary Day' }}
                    </h5>
                </div>

                <form action="{{ route('admin.trips.itinerary.store', $trip->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم اليوم' : 'Day Number' }} <span class="text-danger">*</span></label>
                        <input type="number" name="day_number" class="form-control" value="{{ $trip->itineraries->count() + 1 }}" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عنوان اليوم / النشاط' : 'Day Title' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: الوصول والاستقبال في المطار' : 'e.g. Arrival & Hotel Check-in' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تفاصيل الجدول والأنشطة' : 'Day Activities Description' }}</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="{{ app()->getLocale() == 'ar' ? 'اكتب تفاصيل الفعاليات، الأماكن المزورة، وأوقات التجمع...' : 'Enter activities and details...' }}"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700; padding: 0.6rem;">
                        <i class="fa-solid fa-plus me-1"></i> {{ app()->getLocale() == 'ar' ? 'إضافة هذا اليوم' : 'Add Day' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Itinerary List -->
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-timeline text-primary"></i>
                        <h5 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">
                            {{ app()->getLocale() == 'ar' ? 'جدول الأيام المجدولة' : 'Scheduled Itinerary Days' }}
                        </h5>
                    </div>
                    <small class="text-muted">
                        <i class="fa-solid fa-arrows-up-down me-1"></i> {{ app()->getLocale() == 'ar' ? 'اسحب لإعادة الترتيب' : 'Drag to reorder' }}
                    </small>
                </div>

                @if($trip->itineraries->isEmpty())
                    <div class="alert alert-info text-center py-4 my-3" style="border-radius: var(--radius-md);">
                        <i class="fa-solid fa-circle-info fa-2x mb-2 d-block"></i>
                        {{ app()->getLocale() == 'ar' ? 'لم تتم إضافة أي أيام إلى جدول هذه الرحلة بعد. استخدم النموذج لإضافة أول يوم.' : 'No itinerary days added yet. Use the form on the left to add your first day.' }}
                    </div>
                @else
                    <div class="d-flex flex-column gap-3" id="itinerary-list">
                        @foreach($trip->itineraries as $itinerary)
                            <div class="admin-card p-3 itinerary-item" data-id="{{ $itinerary->id }}" style="border: 1px solid var(--border-light); background: var(--bg-card); transition: all 0.2s ease;">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="sortable-handler mt-1" style="cursor: grab; color: var(--text-muted); font-size: 1.1rem;" title="{{ app()->getLocale() == 'ar' ? 'سحب لإعادة الترتيب' : 'Drag to reorder' }}">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <span class="badge bg-primary me-1 mb-1" style="font-size: 0.8rem; padding: 0.35rem 0.6rem;">
                                                    {{ app()->getLocale() == 'ar' ? 'اليوم' : 'Day' }} {{ $itinerary->day_number }}
                                                </span>
                                                <h6 style="font-weight: 800; color: var(--text-main); display: inline-block; margin: 0;">
                                                    {{ $itinerary->title }}
                                                </h6>
                                            </div>

                                            <div class="d-flex align-items-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon" style="background: rgba(59,130,246,0.1); color: var(--primary);"
                                                        onclick="editItinerary({{ $itinerary->id }}, {{ $itinerary->day_number }}, @js($itinerary->title), @js($itinerary->description))"
                                                        title="{{ app()->getLocale() == 'ar' ? 'تعديل' : 'Edit' }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form action="{{ route('admin.trips.itinerary.destroy', $itinerary->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من حذف هذا اليوم من المسار؟' : 'Are you sure you want to delete this itinerary day?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;" title="{{ app()->getLocale() == 'ar' ? 'حذف' : 'Delete' }}">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if($itinerary->description)
                                            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.5rem 0 0; line-height: 1.5;">
                                                {{ $itinerary->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Itinerary Modal -->
<div class="modal fade" id="editItineraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
            <div class="modal-header border-bottom" style="border-color: var(--border-light) !important;">
                <h5 class="modal-title font-bold text-main" style="font-size: 1.1rem;">
                    {{ app()->getLocale() == 'ar' ? 'تعديل بيانات اليوم' : 'Edit Itinerary Day' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItineraryForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'رقم اليوم' : 'Day Number' }} <span class="text-danger">*</span></label>
                        <input type="number" id="edit_day_number" name="day_number" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'عنوان اليوم / النشاط' : 'Day Title' }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_title" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-bold text-sm">{{ app()->getLocale() == 'ar' ? 'تفاصيل الجدول والأنشطة' : 'Description' }}</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--border-light) !important;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: var(--radius-md);">
                        {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Close' }}
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; border-radius: var(--radius-md); font-weight: 700;">
                        <i class="fa-solid fa-save me-1"></i> {{ app()->getLocale() == 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('itinerary-list');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                handle: '.sortable-handler',
                ghostClass: 'opacity-50',
                onEnd: function() {
                    let order = [];
                    document.querySelectorAll('.itinerary-item').forEach(item => {
                        order.push(item.getAttribute('data-id'));
                    });

                    fetch("{{ route('admin.trips.itinerary.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order : order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (window.Notify) {
                                Notify.success(data.message || '{{ app()->getLocale() == "ar" ? "تم إعادة الترتيب بنجاح" : "Reordered successfully" }}');
                            }
                        } else {
                            if (window.Notify) Notify.error('{{ app()->getLocale() == "ar" ? "فشل إعادة الترتيب" : "Failed to reorder" }}');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (window.Notify) Notify.error('{{ app()->getLocale() == "ar" ? "خطأ في الاتصال بالسيرفر" : "Server communication error" }}');
                    });
                }
            });
        }
    });

    function editItinerary(id, dayNumber, title, description) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_day_number').value = dayNumber;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_description').value = description || '';
        new bootstrap.Modal(document.getElementById('editItineraryModal')).show();
    }

    document.getElementById('editItineraryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_id').value;
        const form = this;
        const formData = new FormData(form);

        fetch(`{{ url('admin/trips/itinerary') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                if (window.Notify) Notify.success('{{ app()->getLocale() == "ar" ? "تم تحديث اليوم بنجاح" : "Day updated successfully" }}');
                setTimeout(() => location.reload(), 500);
            } else {
                if (window.Notify) Notify.error(data.message || 'Error');
            }
        })
        .catch(err => {
            location.reload();
        });
    });
</script>
@endpush
