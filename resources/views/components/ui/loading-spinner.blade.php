@props([
    'size' => 'md', // xs, sm, md, lg, xl
    'type' => 'border', // border, grow
    'color' => 'primary', // primary, secondary, success, danger, warning, info
    'label' => 'Loading...',
    'showLabel' => true,
    'centered' => false,
])

@php
    $sizeClasses = match($size) {
        'xs' => 'spinner-border-sm',
        'sm' => 'spinner-border-sm',
        'lg' => 'spinner-border-lg',
        'xl' => 'spinner-border-xl',
        default => ''
    };
    
    $typeClass = $type === 'grow' ? 'spinner-grow' : 'spinner-border';
    $colorClass = "text-$color";
    $wrapperClass = $centered ? 'd-flex justify-content-center align-items-center' : '';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <div class="{{ $typeClass }} {{ $sizeClasses }} {{ $colorClass }}" role="status">
        <span class="visually-hidden">{{ $label }}</span>
    </div>
    @if($showLabel)
        <span class="ms-2">{{ $label }}</span>
    @endif
</div>