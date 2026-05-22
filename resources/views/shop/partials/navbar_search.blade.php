@php
    use App\Http\Controllers\ProductController;
    $searchValue = trim((string) request('q', ''));
    $clearSearchUrl = request()->routeIs('products')
        ? route('products', ProductController::catalogParams(request(), ['q' => null]))
        : route('products');
@endphp
<div class="navbar-search-wrap" data-search-autocomplete>
    <form method="GET" action="{{ route('products') }}" class="navbar-search-form" role="search">
        <label class="site-search navbar-search" for="navbar-search-input">
            <span class="site-search__icon" aria-hidden="true"><i class="fas fa-search"></i></span>
            <input
                type="search"
                id="navbar-search-input"
                name="q"
                class="site-search__input"
                value="{{ $searchValue }}"
                placeholder="Поиск игр"
                autocomplete="off"
                enterkeyhint="search"
                data-search-input
            >
            @if($searchValue !== '')
            <a href="{{ $clearSearchUrl }}" class="site-search__clear" title="Очистить поиск" aria-label="Очистить поиск" data-search-clear>
                <i class="fas fa-times"></i>
            </a>
            @else
            <button type="button" class="site-search__clear d-none" title="Очистить поиск" aria-label="Очистить поиск" data-search-clear>
                <i class="fas fa-times"></i>
            </button>
            @endif
        </label>
    </form>
    <div class="search-suggestions d-none" data-search-suggestions role="listbox" aria-label="Подсказки поиска"></div>
</div>

@push('scripts')
<script>
    window.playggSearch = {
        endpoint: @json(route('products.search')),
        catalogUrl: @json(route('products')),
        minChars: 1,
    };
</script>
<script src="{{ asset('scripts/search-autocomplete.js') }}" defer></script>
@endpush
