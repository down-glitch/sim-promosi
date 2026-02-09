@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'actions' => null,
])

<div class="page-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="flex-1">
            <h1 class="page-title-large mb-1 fw-bold">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>
        @if($actions)
            <div class="d-flex gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>