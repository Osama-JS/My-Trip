@extends('frontend.agent.layouts.agent-layout')

@section('title', __('Edit Trip'))
@section('page-title', __('Edit Trip'))

@section('content')
@push('styles')
<style>
/* ── Reuse create page styles (same design system) ── */

.atrip-form-container { width: 100%; max-width: 100%; margin: 0; }

.atrip-page-banner {
    background: linear-gradient(135deg, #10b981, #059669, #34d399);
    border-radius: var(--radius-2xl);
    padding: 36px 40px;
    margin-bottom: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(16, 185, 129, 0.25);
}
.atrip-page-banner::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -15%;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
    pointer-events: none;
}
.atrip-page-banner h2 { font-size: 1.6rem; font-weight: 900; margin: 0 0 6px; color: #fff; letter-spacing: -0.3px; }
.atrip-page-banner p { font-size: 0.95rem; color: rgba(255, 255, 255, 0.85); margin: 0; font-weight: 500; }
.atrip-page-banner .banner-icon {
    position: absolute; top: 50%; right: 40px; transform: translateY(-50%);
    font-size: 4rem; color: rgba(255, 255, 255, 0.1);
}

.atrip-card {
    background: var(--bg-card); border-radius: var(--radius-xl); border: 1px solid var(--border-soft);
    box-shadow: var(--shadow-sm); margin-bottom: 24px; overflow: hidden;
    transition: box-shadow var(--transition-fast), border-color var(--transition-fast);
}
.atrip-card:hover { box-shadow: var(--shadow-md); border-color: var(--border); }

.atrip-card-header {
    padding: 22px 28px; background: var(--bg-card); border-bottom: 1px solid var(--border-soft);
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
}
.atrip-card-header .hdr-left { display: flex; align-items: center; gap: 14px; }
.atrip-card-header .hdr-icon {
    width: 42px; height: 42px; background: rgba(16,185,129,0.08); color: #10b981;
    border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0; transition: var(--transition-fast);
}
.atrip-card:hover .hdr-icon { background: #10b981; color: #fff; transform: scale(1.05); }
.atrip-card-header h5 { margin: 0; font-weight: 800; color: var(--text-primary); font-size: 1.05rem; }
.atrip-card-header .hdr-subtitle { font-size: 0.82rem; color: var(--text-muted); font-weight: 500; margin: 2px 0 0; }

.atrip-card-body { padding: 28px; }

.atrip-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 22px; margin-bottom: 22px; }
.atrip-row-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-bottom: 22px; }
.atrip-row:last-child, .atrip-row-3:last-child { margin-bottom: 0; }

.atrip-field { position: relative; }
.atrip-field .field-label { display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px; }
.atrip-field .field-label i { color: #10b981; font-size: 0.78rem; opacity: 0.8; }
.atrip-field .field-label .required-dot { width: 5px; height: 5px; background: #ef4444; border-radius: 50%; margin-inline-start: 4px; flex-shrink: 0; }

.atrip-input {
    width: 100%; padding: 13px 16px; border-radius: var(--radius-md); border: 1.5px solid var(--border);
    background: var(--bg-card); color: var(--text-primary); font-size: 0.93rem; font-family: inherit;
    transition: all var(--transition-fast); outline: none; -webkit-appearance: none;
}
.atrip-input::placeholder { color: var(--text-muted); }
.atrip-input:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
.atrip-input:hover:not(:focus) { border-color: var(--text-muted); }

/* ─── SELECT2 PREMIUM THEME (Edit Page) ─── */
.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    height: 48px !important;
    border-radius: var(--radius-md) !important;
    border: 1.5px solid var(--border) !important;
    background: var(--bg-card) !important;
    padding: 8px 14px !important;
    display: flex !important;
    align-items: center !important;
    transition: all var(--transition-fast) !important;
}
.select2-container--default .select2-selection--single:hover { border-color: var(--text-muted) !important; }
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary) !important;
    font-size: 0.93rem !important;
    font-weight: 600 !important;
    line-height: normal !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--text-muted) !important;
    font-weight: 500 !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    width: 32px !important;
    top: 0 !important;
    right: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
html[dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow {
    right: auto !important;
    left: 8px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border: none !important;
    width: 14px !important;
    height: 14px !important;
    margin: 0 !important;
    top: auto !important;
    left: auto !important;
    position: static !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%2310b981' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-size: contain !important;
    transition: transform var(--transition-fast) !important;
}
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    transform: rotate(180deg) !important;
}
.select2-dropdown {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-lg) !important;
    background: var(--bg-card) !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12) !important;
    overflow: hidden !important;
    z-index: 99999 !important;
}
.select2-container--default .select2-search--dropdown {
    padding: 10px !important;
    background: var(--bg-body) !important;
    border-bottom: 1px solid var(--border-soft) !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-md) !important;
    padding: 8px 12px !important;
    background: var(--bg-card) !important;
    color: var(--text-primary) !important;
    font-size: 0.88rem !important;
    outline: none !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #10b981 !important;
}
.select2-results__options { max-height: 240px !important; padding: 6px !important; }
.select2-container--default .select2-results__option {
    padding: 10px 14px !important;
    border-radius: var(--radius-md) !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    color: var(--text-primary) !important;
    margin-bottom: 2px !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: #10b981 !important;
    color: #fff !important;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #10b981 !important;
    font-weight: 700 !important;
}

/* ─── Language Switcher Tabs ─── */
.lang-tabs {
    display: inline-flex;
    background: var(--bg-body);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 3px;
    gap: 4px;
}
.lang-tab-btn {
    border: none;
    background: transparent;
    padding: 6px 14px;
    border-radius: calc(var(--radius-md) - 3px);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    gap: 6px;
}
.lang-tab-btn:hover { color: var(--text-primary); }
.lang-tab-btn.active {
    background: #10b981;
    color: #fff;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.lang-pane { display: none; }
.lang-pane.active { display: block; animation: fadeIn 0.25s ease-in-out; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

.ck-editor__editable { min-height: 240px; border-radius: 0 0 var(--radius-md) var(--radius-md) !important; border: 1.5px solid var(--border) !important; background: var(--bg-card) !important; color: var(--text-primary) !important; }
.ck-toolbar { border-radius: var(--radius-md) var(--radius-md) 0 0 !important; border: 1.5px solid var(--border) !important; border-bottom: none !important; background: var(--bg-body) !important; }

.atrip-chips-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
.atrip-chip { position: relative; cursor: pointer; }
.atrip-chip input { position: absolute; opacity: 0; width: 0; height: 0; }
.atrip-chip .chip-label {
    padding: 11px 14px; background: var(--bg-card); border: 1.5px solid var(--border); border-radius: var(--radius-md);
    text-align: center; font-size: 0.83rem; font-weight: 600; color: var(--text-secondary);
    transition: all var(--transition-fast); display: flex; align-items: center; justify-content: center; gap: 6px;
}
.atrip-chip .chip-label::before {
    content: ''; width: 16px; height: 16px; border: 2px solid var(--border); border-radius: 4px; flex-shrink: 0; transition: all var(--transition-fast);
}
.atrip-chip:hover .chip-label { border-color: #10b981; background: rgba(16,185,129,0.08); }
.atrip-chip input:checked + .chip-label { background: #10b981; border-color: #10b981; color: #fff; box-shadow: 0 4px 12px rgba(16,185,129,0.25); }
.atrip-chip input:checked + .chip-label::before {
    background: #fff; border-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2310b981'%3E%3Cpath d='M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z'/%3E%3C/svg%3E");
    background-size: 10px; background-repeat: no-repeat; background-position: center;
}

/* ─── Media Upload Zone ─── */
.atrip-upload-dropzone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-xl);
    padding: 28px 20px;
    text-align: center;
    background: var(--bg-body);
    transition: all var(--transition-fast);
    cursor: pointer;
    position: relative;
}
.atrip-upload-dropzone:hover {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.04);
}
.atrip-upload-dropzone .upload-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--bg-card);
    color: #10b981;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    box-shadow: var(--shadow-sm);
    margin-bottom: 10px;
}
.atrip-upload-dropzone h6 { font-weight: 800; margin: 0 0 4px; color: var(--text-primary); font-size: 0.95rem; }
.atrip-upload-dropzone p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }
.atrip-file-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }

.preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 14px; margin-top: 16px; }
.preview-card {
    position: relative; border-radius: var(--radius-md); overflow: hidden; border: 1.5px solid var(--border);
    background: var(--bg-card); aspect-ratio: 16/10; box-shadow: var(--shadow-sm);
}
.preview-card img { width: 100%; height: 100%; object-fit: cover; }
.preview-badge {
    position: absolute; top: 6px; right: 6px; background: rgba(0,0,0,0.7); color: #fff;
    font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; backdrop-filter: blur(4px);
}
.preview-remove {
    position: absolute; top: 6px; left: 6px; width: 24px; height: 24px; background: #ef4444; color: #fff;
    border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.72rem;
    border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.atrip-switch-group { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--bg-body); border-radius: var(--radius-md); border: 1.5px solid var(--border-soft); transition: var(--transition-fast); }
.atrip-switch-group:hover { border-color: var(--border); background: var(--bg-card); }
.atrip-switch-info .switch-title { font-weight: 700; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 2px; }
.atrip-switch-info .switch-desc { font-size: 0.8rem; color: var(--text-muted); }

.atrip-toggle { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
.atrip-toggle input { opacity: 0; width: 0; height: 0; }
.atrip-toggle .toggle-track { position: absolute; cursor: pointer; inset: 0; background: var(--border); transition: var(--transition-fast); border-radius: 26px; }
.atrip-toggle .toggle-track::before { content: ''; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px; background: #fff; transition: var(--transition-fast); border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
.atrip-toggle input:checked + .toggle-track { background: #10b981; }
.atrip-toggle input:checked + .toggle-track::before { transform: translateX(22px); }

.atrip-actions { display: flex; align-items: center; justify-content: flex-end; gap: 14px; padding: 24px 0 40px; }
.atrip-btn { padding: 14px 36px; border-radius: var(--radius-md); font-weight: 800; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px; transition: all var(--transition-spring); cursor: pointer; border: none; text-decoration: none; font-family: inherit; line-height: 1; }
.atrip-btn-save { background: #10b981; color: #fff; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25); }
.atrip-btn-save:hover { background: #059669; transform: translateY(-3px); box-shadow: 0 12px 32px rgba(16, 185, 129, 0.35); }
.atrip-btn-ghost { background: var(--bg-card); color: var(--text-secondary); border: 2px solid var(--border); }
.atrip-btn-ghost:hover { background: var(--bg-body); border-color: var(--text-muted); color: var(--text-primary); transform: translateY(-2px); }

.atrip-error { color: #ef4444; font-size: 0.8rem; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
.atrip-error i { font-size: 0.72rem; }
.atrip-input.is-invalid { border-color: #ef4444; }

.atrip-edit-meta {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    padding: 12px 0 0; font-size: 0.82rem; color: rgba(255,255,255,0.7);
}
.atrip-edit-meta span { display: flex; align-items: center; gap: 5px; }
.atrip-edit-meta i { font-size: 0.75rem; }

@media (max-width: 1024px) { .atrip-row-3 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) {
    .atrip-page-banner { padding: 28px 24px; }
    .atrip-page-banner h2 { font-size: 1.3rem; }
    .atrip-page-banner .banner-icon { display: none; }
    .atrip-card-body { padding: 20px; }
    .atrip-card-header { padding: 18px 20px; flex-direction: column; align-items: flex-start; }
    .atrip-row, .atrip-row-3 { grid-template-columns: 1fr; }
    .atrip-actions { flex-direction: column-reverse; }
    .atrip-btn { width: 100%; justify-content: center; }
    .atrip-chips-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); }
}
</style>
@endpush

<div class="atrip-form-container">

    {{-- Page Banner (Green for Edit) --}}
    <div class="atrip-page-banner">
        <h2><i class="fas fa-pen-fancy" style="margin-inline-end:8px; opacity:0.8;"></i>{{ __('Edit Trip') }}</h2>
        <p>{{ __('Modify the details of your trip package') }}</p>
        <div class="atrip-edit-meta">
            <span><i class="fas fa-hashtag"></i> ID: {{ $trip->id }}</span>
            <span><i class="fas fa-calendar-check"></i> {{ __('Created') }}: {{ $trip->created_at->format('d M Y') }}</span>
            @if($trip->bookings->count() > 0)
                <span><i class="fas fa-ticket-alt"></i> {{ $trip->bookings->count() }} {{ __('Bookings') }}</span>
            @endif
        </div>
        <i class="fas fa-edit banner-icon"></i>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,0.08); border:1.5px solid rgba(239,68,68,0.2); border-radius:var(--radius-md); padding:16px 20px; margin-bottom:24px;">
        <p style="font-weight:700; color:#ef4444; margin:0 0 8px; font-size:0.9rem;"><i class="fas fa-exclamation-triangle" style="margin-inline-end:6px;"></i>{{ __('Please fix the following errors:') }}</p>
        <ul style="margin:0; padding-inline-start:20px; color:#dc2626; font-size:0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('agent.trips.update', $trip->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── Section 1: General Information & Bilingual Inputs ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <div class="hdr-left">
                    <span class="hdr-icon"><i class="fas fa-file-alt"></i></span>
                    <div>
                        <h5>{{ __('General Information') }}</h5>
                        <p class="hdr-subtitle">{{ __('Basic details, categories, and bilingual description') }}</p>
                    </div>
                </div>
                {{-- Language Switcher Tab Buttons --}}
                <div class="lang-tabs">
                    <button type="button" class="lang-tab-btn active" data-lang="ar">
                        <span>🇸🇦</span> {{ __('Arabic') }}
                    </button>
                    <button type="button" class="lang-tab-btn" data-lang="en">
                        <span>🇬🇧</span> {{ __('English') }}
                    </button>
                </div>
            </div>
            <div class="atrip-card-body">
                
                {{-- Arabic Language Content --}}
                <div class="lang-pane active" id="pane-ar">
                    <div class="atrip-row">
                        <div class="atrip-field">
                            <label class="field-label">
                                <i class="fas fa-heading"></i>
                                {{ __('Trip Title (Arabic)') }}
                                <span class="required-dot"></span>
                            </label>
                            <input type="text" name="title_ar" class="atrip-input @error('title_ar') is-invalid @enderror"
                                   value="{{ old('title_ar', $trip->title_ar ?? $trip->title) }}" required>
                            @error('title_ar')
                                <span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="atrip-field">
                            <label class="field-label"><i class="fas fa-tags"></i>{{ __('Trip Categories') }}</label>
                            <div class="atrip-chips-grid">
                                @foreach($categories as $category)
                                    <label class="atrip-chip">
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                            {{ (collect(old('category_ids', $trip->categories->pluck('id')->toArray()))->contains($category->id)) ? 'checked' : '' }}>
                                        <div class="chip-label">{{ $category->name }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-align-left"></i>{{ __('Detailed Description (Arabic)') }}</label>
                        <textarea id="description_ar" name="description_ar" class="atrip-input">{{ old('description_ar', $trip->description_ar ?? $trip->description) }}</textarea>
                    </div>
                </div>

                {{-- English Language Content --}}
                <div class="lang-pane" id="pane-en">
                    <div class="atrip-row">
                        <div class="atrip-field">
                            <label class="field-label">
                                <i class="fas fa-heading"></i>
                                {{ __('Trip Title (English)') }}
                            </label>
                            <input type="text" name="title_en" class="atrip-input @error('title_en') is-invalid @enderror"
                                   value="{{ old('title_en', $trip->title_en) }}">
                            @error('title_en')
                                <span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="atrip-field" style="display:flex; align-items:center;">
                            <p style="margin:0; font-size:0.85rem; color:var(--text-muted); line-height:1.5;">
                                <i class="fas fa-info-circle" style="color:#10b981; margin-inline-end:6px;"></i>
                                {{ __('If left empty, the Arabic title and description will be used automatically.') }}
                            </p>
                        </div>
                    </div>

                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-align-left"></i>{{ __('Detailed Description (English)') }}</label>
                        <textarea id="description_en" name="description_en" class="atrip-input">{{ old('description_en', $trip->description_en) }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Section 2: Media & Image Upload ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <div class="hdr-left">
                    <span class="hdr-icon"><i class="fas fa-images"></i></span>
                    <div>
                        <h5>{{ __('Trip Photos & Media') }}</h5>
                        <p class="hdr-subtitle">{{ __('Current photos and upload new media') }}</p>
                    </div>
                </div>
                <a href="{{ route('agent.trips.show', $trip->id) }}" class="atrip-btn atrip-btn-ghost" style="padding:8px 16px; font-size:0.82rem;">
                    <i class="fas fa-external-link-alt"></i> {{ __('Manage Gallery') }}
                </a>
            </div>
            <div class="atrip-card-body">
                {{-- Existing Images Preview --}}
                @if($trip->images->count() > 0)
                <label class="field-label" style="margin-bottom:12px;"><i class="fas fa-photo-video"></i>{{ __('Current Photos') }} ({{ $trip->images->count() }})</label>
                <div class="preview-grid" style="margin-bottom:24px;">
                    @foreach($trip->images as $index => $img)
                        <div class="preview-card">
                            <img src="{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}" alt="Trip Photo">
                            @if($index === 0)
                                <span class="preview-badge">{{ __('Main') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif

                <div class="atrip-row">
                    {{-- Upload New Thumbnail --}}
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-image"></i>{{ __('Upload Additional Cover Image') }}</label>
                        <div class="atrip-upload-dropzone">
                            <input type="file" name="thumbnail" id="thumbnailInput" class="atrip-file-input" accept="image/*">
                            <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <h6>{{ __('Click or Drag & Drop Image') }}</h6>
                            <p>{{ __('Upload a new photo for this trip') }}</p>
                        </div>
                        <div id="thumbPreviewContainer" style="display:none; margin-top:12px;">
                            <div class="preview-card" style="max-width:240px;">
                                <img id="thumbPreviewImg" src="" alt="Thumbnail Preview">
                                <span class="preview-badge">{{ __('New') }}</span>
                                <button type="button" class="preview-remove" id="thumbRemoveBtn"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Multiple Gallery Photos --}}
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-photo-video"></i>{{ __('Upload More Gallery Photos') }}</label>
                        <div class="atrip-upload-dropzone">
                            <input type="file" name="images[]" id="galleryInput" class="atrip-file-input" accept="image/*" multiple>
                            <div class="upload-icon"><i class="fas fa-images"></i></div>
                            <h6>{{ __('Upload Multiple Photos') }}</h6>
                            <p>{{ __('Select multiple photos to append to gallery') }}</p>
                        </div>
                        <div class="preview-grid" id="galleryPreviewContainer"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 3: Logistics & Pricing ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <div class="hdr-left">
                    <span class="hdr-icon"><i class="fas fa-map-marked-alt"></i></span>
                    <div>
                        <h5>{{ __('Logistics & Pricing') }}</h5>
                        <p class="hdr-subtitle">{{ __('Route, pricing, and capacity details') }}</p>
                    </div>
                </div>
            </div>
            <div class="atrip-card-body">
                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-plane-departure"></i>{{ __('Departure From') }}<span class="required-dot"></span></label>
                        <select name="from_country_id" id="from_country_id" class="atrip-input select2" data-placeholder="{{ __('Select Country') }}" required>
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('from_country_id', $trip->from_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-plane-arrival"></i>{{ __('Destination To') }}<span class="required-dot"></span></label>
                        <select name="to_country_id" id="to_country_id" class="atrip-input select2" data-placeholder="{{ __('Select Country') }}" required>
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('to_country_id', $trip->to_country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-city"></i>{{ __('From City') }}<span class="required-dot"></span></label>
                        <select name="from_city_id" id="from_city_id" class="atrip-input select2" data-placeholder="{{ __('Select City') }}" required>
                            <option value="">{{ __('Select City') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-country="{{ $city->country_id }}" {{ old('from_city_id', $trip->from_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-map-marker-alt"></i>{{ __('Destination City') }}<span class="required-dot"></span></label>
                        <select name="to_city_id" id="to_city_id" class="atrip-input select2" data-placeholder="{{ __('Select City') }}" required>
                            <option value="">{{ __('Select City') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-country="{{ $city->country_id }}" {{ old('to_city_id', $trip->to_city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="atrip-row-3">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-tag"></i>{{ __('Selling Price') }} (SAR)<span class="required-dot"></span></label>
                        <input type="number" step="0.01" name="price" class="atrip-input @error('price') is-invalid @enderror" value="{{ old('price', $trip->price) }}" required>
                        @error('price')<span class="atrip-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-tag"></i>{{ __('Original Price') }} (SAR)</label>
                        <input type="number" step="0.01" name="price_before_discount" class="atrip-input" value="{{ old('price_before_discount', $trip->price_before_discount) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-ticket-alt"></i>{{ __('Tickets Info') }}</label>
                        <input type="text" name="tickets" class="atrip-input" value="{{ old('tickets', $trip->tickets) }}">
                    </div>
                </div>

                <div class="atrip-row-3">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-users"></i>{{ __('Max Total Capacity') }}</label>
                        <input type="number" name="personnel_capacity" class="atrip-input" value="{{ old('personnel_capacity', $trip->personnel_capacity) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-user-friends"></i>{{ __('Base Included Capacity') }}</label>
                        <input type="number" name="base_capacity" class="atrip-input" value="{{ old('base_capacity', $trip->base_capacity) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-user-plus"></i>{{ __('Extra Pax Price') }} (SAR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="extra_passenger_price" class="atrip-input" value="{{ old('extra_passenger_price', $trip->extra_passenger_price ?? 0) }}" required>
                    </div>
                </div>

                <div class="atrip-row">
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-clock"></i>{{ __('Duration') }}</label>
                        <input type="text" name="duration" class="atrip-input" value="{{ old('duration', $trip->duration) }}">
                    </div>
                    <div class="atrip-field">
                        <label class="field-label"><i class="fas fa-calendar-alt"></i>{{ __('Expiry Date') }}</label>
                        <input type="date" name="expiry_date" class="atrip-input" value="{{ old('expiry_date', $trip->expiry_date ? $trip->expiry_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Section 4: Settings & Visibility ── --}}
        <div class="atrip-card">
            <div class="atrip-card-header">
                <div class="hdr-left">
                    <span class="hdr-icon"><i class="fas fa-sliders-h"></i></span>
                    <div>
                        <h5>{{ __('Settings & Visibility') }}</h5>
                        <p class="hdr-subtitle">{{ __('Control who can see and book this trip') }}</p>
                    </div>
                </div>
            </div>
            <div class="atrip-card-body">
                <div class="atrip-row">
                    <div class="atrip-switch-group">
                        <div class="atrip-switch-info">
                            <div class="switch-title">{{ __('Public Visibility') }}</div>
                            <div class="switch-desc">{{ __('Make this trip visible to all users') }}</div>
                        </div>
                        <label class="atrip-toggle">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $trip->is_public) ? 'checked' : '' }}>
                            <span class="toggle-track"></span>
                        </label>
                    </div>
                    <div class="atrip-switch-group">
                        <div class="atrip-switch-info">
                            <div class="switch-title">{{ __('Active Status') }}</div>
                            <div class="switch-desc">{{ __('Enable or disable bookings') }}</div>
                        </div>
                        <label class="atrip-toggle">
                            <input type="checkbox" name="active" value="1" {{ old('active', $trip->active) ? 'checked' : '' }}>
                            <span class="toggle-track"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="atrip-actions">
            <a href="{{ route('agent.trips.index') }}" class="atrip-btn atrip-btn-ghost">
                <i class="fas fa-times"></i> {{ __('Cancel') }}
            </a>
            <button type="submit" class="atrip-btn atrip-btn-save">
                <i class="fas fa-save"></i> {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
$(document).ready(function () {
    const isRtl = $('html').attr('dir') === 'rtl';

    // ─── 1. Select2 Initialization ───
    $('.select2').select2({
        width: '100%',
        dir: isRtl ? 'rtl' : 'ltr',
        placeholder: function() {
            return $(this).data('placeholder') || "{{ __('Select an option') }}";
        },
        allowClear: true
    });

    // ─── 2. CKEditor Initialization ───
    const editorConfig = {
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ]
    };

    if (document.querySelector('#description_ar')) {
        ClassicEditor.create(document.querySelector('#description_ar'), {
            ...editorConfig,
            language: 'ar'
        }).catch(err => console.error('CKEditor AR Error:', err));
    }

    if (document.querySelector('#description_en')) {
        ClassicEditor.create(document.querySelector('#description_en'), {
            ...editorConfig,
            language: 'en'
        }).catch(err => console.error('CKEditor EN Error:', err));
    }

    // ─── 3. Language Switcher Tabs ───
    $('.lang-tab-btn').on('click', function () {
        $('.lang-tab-btn').removeClass('active');
        $(this).addClass('active');

        const lang = $(this).attr('data-lang');
        $('.lang-pane').removeClass('active');
        $('#pane-' + lang).addClass('active');
    });

    // ─── 4. Cascading Select2 Country -> City Dropdowns ───
    function initCascadingDropdown(countrySelectId, citySelectId) {
        const $country = $('#' + countrySelectId);
        const $city = $('#' + citySelectId);
        if (!$country.length || !$city.length) return;

        const allCityOptions = [];
        $city.find('option').each(function() {
            const val = $(this).val();
            if (val) {
                allCityOptions.push({
                    value: val,
                    text: $(this).text(),
                    countryId: $(this).attr('data-country'),
                    selected: $(this).is(':selected')
                });
            }
        });

        function filterCities() {
            const selectedCountryId = $country.val();
            const currentSelectedCity = $city.val();

            $city.empty();
            $city.append(new Option("{{ __('Select City') }}", '', false, false));

            let matched = 0;
            allCityOptions.forEach(opt => {
                if (!selectedCountryId || opt.countryId === selectedCountryId) {
                    const isSelected = (opt.value === currentSelectedCity || (opt.selected && !selectedCountryId));
                    const newOpt = new Option(opt.text, opt.value, isSelected, isSelected);
                    $(newOpt).attr('data-country', opt.countryId || '');
                    $city.append(newOpt);
                    matched++;
                }
            });

            $city.prop('disabled', matched === 0 && selectedCountryId !== '');
            $city.trigger('change.select2');
        }

        $country.on('change', filterCities);
        if ($country.val()) {
            filterCities();
        }
    }

    initCascadingDropdown('from_country_id', 'from_city_id');
    initCascadingDropdown('to_country_id', 'to_city_id');

    // ─── 5. Image Upload & Live Previews ───
    const thumbInput = document.getElementById('thumbnailInput');
    const thumbContainer = document.getElementById('thumbPreviewContainer');
    const thumbImg = document.getElementById('thumbPreviewImg');
    const thumbRemove = document.getElementById('thumbRemoveBtn');

    if (thumbInput) {
        thumbInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    thumbImg.src = e.target.result;
                    thumbContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (thumbRemove) {
        thumbRemove.addEventListener('click', function (e) {
            e.stopPropagation();
            thumbInput.value = '';
            thumbImg.src = '';
            thumbContainer.style.display = 'none';
        });
    }

    const galleryInput = document.getElementById('galleryInput');
    const galleryContainer = document.getElementById('galleryPreviewContainer');

    if (galleryInput) {
        galleryInput.addEventListener('change', function () {
            galleryContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const card = document.createElement('div');
                        card.className = 'preview-card';
                        card.innerHTML = `
                            <img src="${e.target.result}" alt="Gallery ${index+1}">
                            <span class="preview-badge">${(file.size / 1024).toFixed(0)} KB</span>
                        `;
                        galleryContainer.appendChild(card);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }
});
</script>
@endpush
