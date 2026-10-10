@php
    $isAr = app()->getLocale() == 'ar';
    $opts = $perPageOptions ?? [10, 15, 25, 50, 100];
    $cur = (int) $paginator->perPage();
    if (!in_array($cur, $opts)) { $opts[] = $cur; sort($opts); }
@endphp
<div class="v2-per-page">
    <span>{{ $isAr ? 'عرض' : 'Show' }}</span>
    <select class="v2-per-page-select no-select2 form-select" onchange="(function(s){var u=new URL(window.location.href);u.searchParams.set('per_page',s.value);u.searchParams.delete('page');window.location.href=u.toString();})(this)">
        @foreach($opts as $o)
            <option value="{{ $o }}" {{ $o == $cur ? 'selected' : '' }}>{{ $o }}</option>
        @endforeach
    </select>
    <span>{{ $isAr ? 'سجل' : 'entries' }}</span>
</div>
