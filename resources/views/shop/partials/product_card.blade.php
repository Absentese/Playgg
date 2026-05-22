<div class="card h-100 product-card">
    <div class="position-relative">
        <a href="{{ route('product.show', $product) }}" class="product-photo-wrap product-photo-wrap--card d-block">
            <img src="{{ $product->imageUrl() }}" class="product-photo" alt="{{ $product->name }}">
        </a>
        @if($product->discountPercent())
        <span class="badge badge-deal position-absolute top-0 start-0 m-2">-{{ $product->discountPercent() }}%</span>
        @endif
        @if($product->is_preorder)
        <span class="badge badge-preorder position-absolute top-0 end-0 m-2">Предзаказ</span>
        @endif
        <span class="badge badge-platform position-absolute bottom-0 start-0 m-2">{{ $product->platform }}</span>
    </div>
    <div class="card-body d-flex flex-column">
        <h5 class="card-title product-card__title mb-2">
            <a href="{{ route('product.show', $product) }}" class="text-decoration-none text-white">{{ $product->name }}</a>
        </h5>
        @if($tags = $product->displayTags())
        <div class="product-card__tags mb-2">
            @foreach($tags as $tag)
            @if($tag['url'])
            <a href="{{ $tag['url'] }}" class="product-card-tag">{{ $tag['label'] }}</a>
            @else
            <span class="product-card-tag product-card-tag--static">{{ $tag['label'] }}</span>
            @endif
            @endforeach
        </div>
        @endif
        <p class="product-card__desc small mb-3 flex-grow-1">{{ Str::limit($product->description, 140) }}</p>
        <div class="product-card__footer d-flex justify-content-between align-items-end mt-auto gap-2">
            <div class="product-card__prices">
                @if($product->old_price && $product->old_price > $product->price)
                <span class="product-price-old">{{ number_format($product->old_price, 0, ',', ' ') }}&nbsp;₽</span>
                @endif
                <span class="product-price">{{ number_format($product->price, 0, ',', ' ') }}&nbsp;₽</span>
            </div>
            @auth
            <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-sm btn-accent rounded-pill px-3" title="В корзину">
                    <i class="fas fa-cart-plus"></i>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3" title="Войдите для покупки">
                <i class="fas fa-cart-plus"></i>
            </a>
            @endauth
        </div>
    </div>
</div>
