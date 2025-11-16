<div class="col-md-{{ $cols ?? '3' }}">
    <div class="card stat-card {{ $color }} shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h3 class="mb-0 fw-bold">{{ $value }}</h3>
                    <small class="text-muted">{{ $label }}</small>
                </div>
                <div class="bg-{{ $iconBg }} bg-opacity-10 rounded p-2">
                    <i class="bi bi-{{ $icon }} fs-4 text-{{ $iconBg }}"></i>
                </div>
            </div>
        </div>
    </div>
</div>