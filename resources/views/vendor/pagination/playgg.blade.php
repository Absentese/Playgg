@if ($paginator->hasPages())
<nav class="shop-pagination" role="navigation" aria-label="Пагинация">
    <ul class="pagination shop-pagination__list mb-0">
        @if ($paginator->onFirstPage())
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link"><i class="fas fa-chevron-left"></i> Назад</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i> Назад</a>
        </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
            <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Вперёд <i class="fas fa-chevron-right"></i></a>
        </li>
        @else
        <li class="page-item disabled" aria-disabled="true">
            <span class="page-link">Вперёд <i class="fas fa-chevron-right"></i></span>
        </li>
        @endif
    </ul>

    <p class="shop-pagination__info mb-0">
        Показано {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} из {{ $paginator->total() }}
    </p>
</nav>
@endif
