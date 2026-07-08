@if ($paginator->hasPages())
<style>
    .fe-premium-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin: 2rem 0;
        padding: 0;
        list-style: none;
        flex-wrap: wrap;
    }
    .fe-premium-pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        padding: 0 16px;
        border-radius: 14px;
        background: var(--bg-card, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        color: var(--text-muted, #64748b);
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .fe-premium-pagination .page-item:not(.disabled):not(.active) .page-link:hover {
        background: var(--primary, #001841);
        color: #ffffff;
        border-color: var(--primary, #001841);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 24, 65, 0.2);
    }
    .fe-premium-pagination .page-item.active .page-link {
        background: var(--primary, #001841);
        color: #ffffff;
        border-color: var(--primary, #001841);
        box-shadow: 0 6px 15px rgba(0, 24, 65, 0.25);
        transform: scale(1.05);
    }
    .fe-premium-pagination .page-item.disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        background: var(--bg-main, #f8fafc);
        color: var(--text-muted, #64748b);
    }
    .fe-premium-pagination .page-link i {
        font-size: 0.9rem;
    }
    
    /* Dark Mode Adaptations */
    body.dark-mode .fe-premium-pagination .page-item .page-link {
        background: var(--bg-card, #1e293b);
        border-color: var(--border-color, #334155);
        color: var(--text-muted, #94a3b8);
    }
    body.dark-mode .fe-premium-pagination .page-item:not(.disabled):not(.active) .page-link:hover {
        background: var(--primary, #2563eb);
        color: #ffffff;
        border-color: var(--primary, #2563eb);
    }
    body.dark-mode .fe-premium-pagination .page-item.active .page-link {
        background: var(--primary, #2563eb);
        color: #ffffff;
        border-color: var(--primary, #2563eb);
    }
</style>

    <nav aria-label="Pagination Navigation">
        <ul class="fe-premium-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true"><i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
