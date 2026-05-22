@php
    use App\Http\Controllers\ProductController;
    $searchValue = trim((string) request('q', ''));
    $clearSearchUrl = request()->routeIs('products')
        ? route('products', ProductController::catalogParams(request(), ['q' => null]))
        : route('products');
@endphp
<div class="navbar-search-wrap" data-search-autocomplete data-clear-url="{{ $clearSearchUrl }}">
    <form method="GET" action="{{ route('products') }}" class="navbar-search-form" role="search">
        <label class="site-search navbar-search" for="navbar-search-input">
            <span class="site-search__icon" aria-hidden="true"><i class="fas fa-search"></i></span>
            <input
                type="text"
                id="navbar-search-input"
                name="q"
                class="site-search__input"
                value="{{ $searchValue }}"
                placeholder="Поиск игр"
                autocomplete="off"
                enterkeyhint="search"
                role="searchbox"
                aria-autocomplete="list"
                aria-controls="navbar-search-suggestions"
                data-search-input
            >
            <button
                type="button"
                class="site-search__clear {{ $searchValue === '' ? 'd-none' : '' }}"
                title="Очистить поиск"
                aria-label="Очистить поиск"
                data-search-clear
            >
                <i class="fas fa-times"></i>
            </button>
        </label>
    </form>
    <div id="navbar-search-suggestions" class="search-suggestions d-none" data-search-suggestions role="listbox" aria-label="Подсказки поиска"></div>
</div>

@push('styles')
<style>
    .navbar-search-wrap .search-suggestions {
        display: flex;
        flex-direction: column;
        width: 100%;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .navbar-search-wrap .search-suggestions::-webkit-scrollbar { display: none; width: 0; height: 0; }
    .navbar-search-wrap .search-suggestions__item { display: flex; width: 100%; box-sizing: border-box; }
    .navbar-search-wrap .search-suggestions__all { display: block; width: 100%; }
</style>
@endpush

@push('scripts')
<script>
    window.playggSearch = {
        endpoint: @json(route('products.search')),
        catalogUrl: @json(route('products')),
        minChars: 1,
    };
</script>
<script src="{{ asset('scripts/search-autocomplete.js') }}@if(file_exists(public_path('scripts/search-autocomplete.js')))?v={{ filemtime(public_path('scripts/search-autocomplete.js')) }}@endif" defer></script>
@endpush
