{{-- https://stackoverflow.com/questions/28240777/custom-pagination-view-in-laravel-5 --}}

{{-- Following commands will generate Pagination template in resources/views/vendor/pagination
artisan vendor:publish --tag=laravel-pagination
artisan vendor:publish --}}

{{-- para ver todas las funciones de paginacion visitar https://laravel.com/docs/7.x/pagination --}}

@if ($paginator->hasPages())
    <ul class="uk-pagination uk-flex-center" style="padding: 30px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="uk-disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="uk-disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="uk-active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="uk-disabled"><span>&raquo;</span></li>
        @endif
    </ul>
@endif
