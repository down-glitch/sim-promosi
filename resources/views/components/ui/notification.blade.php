@props([
    'type' => 'info', // success, info, warning, error
    'title' => null,
    'message',
    'showIcon' => true,
    'autoDismiss' => true,
    'duration' => 5000,
    'showClose' => true,
])

@php
    $alertTypes = [
        'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill', 'color' => 'success'],
        'info' => ['class' => 'alert-info', 'icon' => 'bi-info-circle-fill', 'color' => 'info'],
        'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-triangle-fill', 'color' => 'warning'],
        'error' => ['class' => 'alert-danger', 'icon' => 'bi-exclamation-circle-fill', 'color' => 'danger'],
    ];
    
    $currentType = $alertTypes[$type] ?? $alertTypes['info'];
    $iconClass = $showIcon ? '<i class="bi ' . $currentType['icon'] . ' me-2"></i>' : '';
@endphp

<div 
    {{ $attributes->merge(['class' => "alert {$currentType['class']} alert-dismissible fade show"]) }}
    role="alert"
    @if($autoDismiss)
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, {{ $duration }})"
    @endif
>
    <div class="d-flex align-items-center">
        @if($showIcon)
            <i class="bi {{ $currentType['icon'] }} me-2 fs-5 text-{{ $currentType['color'] }}"></i>
        @endif
        <div>
            @if($title)
                <h4 class="alert-heading fw-bold mb-1">{{ $title }}</h4>
            @endif
            <div>{{ $message }}</div>
        </div>
    </div>
    
    @if($showClose)
        <button 
            type="button" 
            class="btn-close" 
            data-bs-dismiss="alert" 
            aria-label="Close"
            @if($autoDismiss) x-on:click="show = false" @endif
        ></button>
    @endif
</div>