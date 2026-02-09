@props([
    'size' => 'md', // sm, md, lg, xl
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'outline' => false,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'href' => null,
])

@php
    $sizeClasses = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        'xl' => 'btn-lg px-4 py-3',
        default => ''
    };

    $variantClasses = $outline 
        ? match($variant) {
            'primary' => 'btn-outline-primary',
            'secondary' => 'btn-outline-secondary',
            'success' => 'btn-outline-success',
            'danger' => 'btn-outline-danger',
            'warning' => 'btn-outline-warning',
            'info' => 'btn-outline-info',
            default => 'btn-outline-' . $variant
        }
        : match($variant) {
            'primary' => 'btn-primary',
            'secondary' => 'btn-secondary',
            'success' => 'btn-success',
            'danger' => 'btn-danger',
            'warning' => 'btn-warning',
            'info' => 'btn-info',
            default => 'btn-' . $variant
        };

    $disabledClass = $disabled || $loading ? 'disabled' : '';
    $loadingIcon = $loading ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' : '';
    $iconHtml = $icon ? '<i class="' . $icon . ' me-2"></i>' : '';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "btn $variantClasses $sizeClasses $disabledClass"]) }}>
        @if($loading) {!! $loadingIcon !!} @endif
        @if($icon) {!! $iconHtml !!} @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => "btn $variantClasses $sizeClasses $disabledClass", 'type' => 'button']) }}>
        @if($loading) {!! $loadingIcon !!} @endif
        @if($icon) {!! $iconHtml !!} @endif
        {{ $slot }}
    </button>
@endif