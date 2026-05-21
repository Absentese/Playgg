@php
    use App\Http\Controllers\ProductController;
    $catalogBase = fn (array $overrides = []) => route('products', ProductController::catalogParams(request(), $overrides));
@endphp

<div class="catalog-panel mb-4">
    <div class="catalog-panel__bar row g-3 align-items-center">
        <div class="col-lg-7">
            <ul class="nav filter-pills flex-wrap">
                <li class="nav-item">
                    <a class="nav-link {{ $currentCategory === 'all' && !request('sale') ? 'active' : '' }}"
                       href="{{ $catalogBase(['category' => null, 'sale' => null, 'preorder' => null]) }}">Все</a>
                </li>
                @foreach($categories as $category)
                <li class="nav-item">
                    <a class="nav-link {{ $currentCategory === $category->slug && !request('sale') ? 'active' : '' }}"
                       href="{{ $catalogBase(['category' => $category->slug, 'sale' => null, 'preorder' => null]) }}">{{ $category->name }}</a>
                </li>
                @endforeach
                <li class="nav-item">
                    <a class="nav-link nav-link--sale {{ request('sale') ? 'active' : '' }}"
                       href="{{ $catalogBase(['sale' => '1', 'category' => null, 'preorder' => null]) }}">
                        <i class="fas fa-fire me-1"></i>Скидки
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-lg-5">
            <form method="GET" action="{{ route('products') }}" class="catalog-sort-form">
                @if($searchQuery !== '')
                <input type="hidden" name="q" value="{{ $searchQuery }}">
                @endif
                @if(request('category') && request('category') !== 'all')
                <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('sale'))
                <input type="hidden" name="sale" value="1">
                @endif
                @if(request('preorder'))
                <input type="hidden" name="preorder" value="1">
                @endif
                <div class="catalog-sort-wrap">
                    <select name="sort" class="form-select catalog-sort" aria-label="Сортировка" onchange="this.form.submit()">
                        <option value="new" @selected($currentSort === 'new')>Новинки</option>
                        <option value="discount" @selected($currentSort === 'discount')>По скидке</option>
                        <option value="price_asc" @selected($currentSort === 'price_asc')>Цена ↑</option>
                        <option value="price_desc" @selected($currentSort === 'price_desc')>Цена ↓</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>
