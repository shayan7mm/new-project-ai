@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li><span class="disabled">‹</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="disabled">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="current">{{ \App\Support\Fa::digits($page) }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ \App\Support\Fa::digits($page) }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a></li>
            @else
                <li><span class="disabled">›</span></li>
            @endif
        </ul>
    </nav>
@endif
