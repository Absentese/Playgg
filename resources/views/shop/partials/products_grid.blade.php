@if($products->isNotEmpty())
<div class="products-grid">
    @foreach($products as $product)
    <div class="products-grid__item">
        @include('shop.partials.product_card', ['product' => $product])
    </div>
    @endforeach
</div>
@endif
