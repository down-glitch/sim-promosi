@props(['pages'])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($pages as $page)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">{{ $page['name'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $page['url'] }}">{{ $page['name'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>