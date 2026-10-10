@php
    $isAr = app()->getLocale() == 'ar';
    $perPageOptions = $perPageOptions ?? [10, 15, 25, 50, 100];
    $current = (int) $paginator->perPage();
    if (!in_array($current, $perPageOptions)) { $perPageOptions[] = $current; sort($perPageOptions); }
    $last = $paginator->lastPage();
    $page = $paginator->currentPage();
    $pages = collect([1, $last, $page - 1, $page, $page + 1])->filter(fn($p) => $p >= 1 && $p <= $last)->unique()->sort()->values();
    $url = fn($p) => $paginator->url($p);
@endphp
<div class="v2-table-footer" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <div class="v2-per-page">
            <span>{{ $isAr ? 'عرض' : 'Show' }}</span>
            <select class="v2-per-page-select" onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.set('page', 1); window.location.href = u.toString();">
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}" {{ $current == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            <span>{{ $isAr ? 'صفوف' : 'rows' }}</span>
        </div>
        <div class="v2-pagination-info" style="font-size:.85rem;font-weight:600;color:var(--text-muted);">
            @if($paginator->total() === 0)
                {{ $isAr ? 'لا توجد سجلات' : 'No entries' }}
            @elseif($isAr)
                عرض {{ $paginator->firstItem() }} إلى {{ $paginator->lastItem() }} من إجمالي {{ $paginator->total() }} سجل
            @else
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
            @endif
        </div>
    </div>
    @if($last > 1)
    <nav class="v2-pagination-nav">
        <a class="v2-page-btn {{ $page == 1 ? 'disabled' : '' }}" href="{{ $url(1) }}" title="{{ $isAr ? 'الأولى' : 'First' }}"><i class="fa-solid fa-angles-{{ $isAr ? 'right' : 'left' }}"></i></a>
        <a class="v2-page-btn {{ $page == 1 ? 'disabled' : '' }}" href="{{ $url(max(1, $page - 1)) }}" title="{{ $isAr ? 'السابق' : 'Previous' }}"><i class="fa-solid fa-chevron-{{ $isAr ? 'right' : 'left' }}"></i></a>
        @php $prev = 0; @endphp
        @foreach($pages as $p)
            @if($p - $prev > 1)<span class="v2-page-btn" style="border:none;background:transparent;cursor:default;">...</span>@endif
            <a class="v2-page-btn {{ $p == $page ? 'active' : '' }}" href="{{ $url($p) }}">{{ $p }}</a>
            @php $prev = $p; @endphp
        @endforeach
        <a class="v2-page-btn {{ $page == $last ? 'disabled' : '' }}" href="{{ $url(min($last, $page + 1)) }}" title="{{ $isAr ? 'التالي' : 'Next' }}"><i class="fa-solid fa-chevron-{{ $isAr ? 'left' : 'right' }}"></i></a>
        <a class="v2-page-btn {{ $page == $last ? 'disabled' : '' }}" href="{{ $url($last) }}" title="{{ $isAr ? 'الأخيرة' : 'Last' }}"><i class="fa-solid fa-angles-{{ $isAr ? 'left' : 'right' }}"></i></a>
    </nav>
    @endif
</div>
