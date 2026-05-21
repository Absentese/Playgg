@php
    use App\Http\Controllers\ProductController;
    $searchValue = trim((string) request('q', ''));
    $clearSearchUrl = request()->routeIs('products')
        ? route('products', ProductController::catalogParams(request(), ['q' => null]))
        : route('products');
@endphp
<form method="GET" action="{{ route('products') }}" class="navbar-search-form" role="search">
    <label class="site-search navbar-search" for="navbar-search-input">
        <span class="site-search__icon" aria-hidden="true"><i class="fas fa-search"></i></span>
        <input
            type="search"
            id="navbar-search-input"
            name="q"
            class="site-search__input"
            value="{{ $searchValue }}"
            placeholder="Поиск"
            autocomplete="off"
            enterkeyhint="search"
        >
        @if($searchValue !== '')
        <a href="{{ $clearSearchUrl }}" class="site-search__clear" title="Очистить поиск" aria-label="Очистить поиск">
            <i class="fas fa-times"></i>
        </a>
        @endif
    </label>
</form>
