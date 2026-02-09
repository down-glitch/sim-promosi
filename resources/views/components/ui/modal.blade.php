@props([
    'id',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl, fullscreen
    'scrollable' => false,
    'centered' => true,
    'backdrop' => 'true', // true, false, static
    'keyboard' => 'true', // true, false
    'show' => false,
    'footer' => null,
    'closeButton' => true,
])

@php
    $sizeClass = match($size) {
        'sm' => 'modal-sm',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        'fullscreen' => 'modal-fullscreen',
        default => ''
    };
    
    $scrollableClass = $scrollable ? 'modal-dialog-scrollable' : '';
    $centeredClass = $centered ? 'modal-dialog-centered' : '';
@endphp

<div 
    wire:key="modal-{{ $id }}"
    class="modal fade" 
    id="{{ $id }}" 
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    @if($show) style="display: block;" data-bs-backdrop="static" data-bs-keyboard="false" @endif
>
    <div class="modal-dialog {{ $sizeClass }} {{ $scrollableClass }} {{ $centeredClass }}">
        <div class="modal-content border-0 shadow-lg">
            @if($title || $closeButton)
                <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #276A2B 0%, #38a169 100%);">
                    @if($title)
                        <h5 class="modal-title fw-bold" id="{{ $id }}Label">{{ $title }}</h5>
                    @endif
                    @if($closeButton)
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endif
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@if($show)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('{{ $id }}');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        </script>
    @endpush
@endif