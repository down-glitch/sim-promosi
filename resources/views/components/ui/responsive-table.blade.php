@props([
    'headers' => [],
    'data' => [],
    'emptyMessage' => 'Data tidak ditemukan',
    'striped' => false,
    'hover' => true,
    'small' => false,
])

@php
    $tableClasses = collect([
        'table',
        $striped ? 'table-striped' : '',
        $hover ? 'table-hover' : '',
        $small ? 'table-sm' : '',
        'align-middle'
    ])->filter()->implode(' ');
@endphp

<div class="table-responsive">
    <table {{ $attributes->merge(['class' => $tableClasses]) }}>
        <thead class="table-success">
            <tr>
                @foreach($headers as $header)
                    <th @isset($header['width']) width="{{ $header['width'] }}" @endisset 
                        @isset($header['class']) class="{{ $header['class'] }}" @endisset>
                        {{ $header['label'] ?? $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(count($data) > 0)
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle fs-1 mb-3"></i>
                        <p class="mb-0">{{ $emptyMessage }}</p>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>