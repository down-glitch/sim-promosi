@props([
    'filters' => [],
    'sortOptions' => [],
    'currentFilters' => [],
    'currentSort' => null,
    'showReset' => true,
    'compact' => false,
])

@php
    $containerClass = $compact ? 'row g-2' : 'row g-3 mb-4';
    $filterClass = $compact ? 'col-md-3 col-sm-6' : 'col-md-4';
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ request()->url() }}">
            <div class="{{ $containerClass }}">
                @foreach($filters as $filter)
                    <div class="{{ $filterClass }}">
                        <label class="form-label fw-semibold">{{ $filter['label'] }}</label>
                        @if($filter['type'] === 'select')
                            <select name="{{ $filter['name'] }}" class="form-select">
                                <option value="">Semua {{ $filter['label'] }}</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input 
                                type="{{ $filter['type'] ?? 'text' }}" 
                                name="{{ $filter['name'] }}" 
                                class="form-control" 
                                placeholder="Cari {{ strtolower($filter['label']) }}"
                                value="{{ request($filter['name']) }}"
                            >
                        @endif
                    </div>
                @endforeach
                
                @if(!empty($sortOptions))
                    <div class="{{ $filterClass }}">
                        <label class="form-label fw-semibold">Urutkan Berdasarkan</label>
                        <select name="sort" class="form-select">
                            <option value="">Default</option>
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('sort') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="{{ $compact ? 'col-md-3 col-sm-6 d-flex align-items-end' : 'col-md-4 d-flex align-items-end' }}">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        @if($showReset)
                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>