@props([
    'type' => 'info', // info, success, warning, danger
    'title' => null,
    'dismissable' => true,
])

@php
    $alertClasses = match($type) {
        'success' => 'alert-success bg-success-subtle border-success-subtle text-success',
        'warning' => 'alert-warning bg-warning-subtle border-warning-subtle text-warning',
        'danger' => 'alert-danger bg-danger-subtle border-danger-subtle text-danger',
        'info' => 'alert-info bg-info-subtle border-info-subtle text-info',
        default => 'alert-info bg-info-subtle border-info-subtle text-info'
    };
@endphp

<div {{ $attributes->merge(['class' => "$alertClasses alert-dismissible fade show"]) }} role="alert">
    @if($title)
        <h4 class="alert-heading fw-bold">{{ $title }}</h4>
    @endif
    <div>
        {{ $slot }}
    </div>
    @if($dismissable)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>