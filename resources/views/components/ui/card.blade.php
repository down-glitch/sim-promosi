@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'headerActions' => null,
    'footer' => null,
    'shadow' => true,
    'border' => true,
    'rounded' => true,
])

@php
    $cardClasses = collect([
        'card',
        $shadow ? 'shadow-sm' : '',
        $border ? 'border-0' : '',
        $rounded ? 'rounded' : '',
    ])->filter()->implode(' ');

    $headerClasses = collect([
        'card-header',
        $border ? 'border-bottom' : '',
        'py-3',
        'px-4'
    ])->filter()->implode(' ');

    $bodyClasses = 'card-body p-4';
    $footerClasses = 'card-footer bg-transparent border-top';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title || $headerActions)
        <div class="{{ $headerClasses }}">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="flex-1">
                    @if($title)
                        <h5 class="card-title mb-1 fw-bold">
                            @if($icon)<i class="{{ $icon }} me-2"></i>@endif
                            {{ $title }}
                        </h5>
                    @endif
                    @if($subtitle)
                        <p class="card-subtitle mb-0 text-muted">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($headerActions)
                    <div>
                        {{ $headerActions }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $bodyClasses }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="{{ $footerClasses }}">
            {{ $footer }}
        </div>
    @endif
</div>