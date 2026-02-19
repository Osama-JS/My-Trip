<style>
/* Stats Cards - White Background */
.stat-card {
    background: #fff;
    border: none;
    border-radius: 16px;
    position: relative;
    transition: all 0.3s ease;
    min-height: 120px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}
.stat-card-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #fff;
    flex-shrink: 0;
}
.stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-icon.warning { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }
.stat-icon.danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.stat-icon.info { background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%); }
.stat-info { flex: 1; }
.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    color: #333;
}
.stat-title {
    font-size: 0.85rem;
    color: #888;
    margin: 0.4rem 0 0;
    font-weight: 500;
}
.stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 15px;
    margin-top: 0.4rem;
}
.trend-up { background: rgba(17, 153, 142, 0.15); color: #11998e; }
.trend-down { background: rgba(235, 51, 73, 0.15); color: #eb3349; }

/* Dark mode */
[data-theme-version="dark"] .stat-card { background: #1e2746; }
[data-theme-version="dark"] .stat-value { color: #fff; }
[data-theme-version="dark"] .stat-title { color: rgba(255,255,255,0.6); }

/* RTL Support */
[dir="rtl"] .stat-card-content { flex-direction: row-reverse; }
[dir="rtl"] .stat-info { text-align: right; }
</style>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-card-content">
                    <div class="stat-icon {{ $color ?? 'primary' }}">
                        <i class="fa {{ $icon ?? 'fa-chart-bar' }}"></i>
                    </div>
                    <div class="stat-info">
                        <h2 class="stat-value">{{ $value }}</h2>
                        <p class="stat-title">{{ $label }}</p>
                        @if(isset($stat['trend']))
                        <span class="stat-trend {{ str_starts_with($stat['trend'], '+') ? 'trend-up' : 'trend-down' }}">
                            <i class="fa {{ str_starts_with($stat['trend'], '+') ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($trend) }}%
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

