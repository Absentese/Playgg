@extends('admin.layouts.app')
@section('title', 'Игры')
@section('page_title', 'Игры')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Добавление игры</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addProductForm">
            {{ request()->has('add') || $errors->any() ? 'Свернуть' : 'Развернуть' }}
        </button>
    </div>
    <div class="collapse {{ request()->has('add') || $errors->any() ? 'show' : '' }}" id="addProductForm">
        <div class="card-body border-top">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Название</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Например: HELLDIVERS 2">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Категория</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Выберите...</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Цена со скидкой, ₽</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" min="0" step="1" required>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Старая цена, ₽</label>
                        <input type="number" name="old_price" class="form-control @error('old_price') is-invalid @enderror" value="{{ old('old_price') }}" min="0" step="1" placeholder="Без скидки">
                        @error('old_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Выше текущей — появится бейдж скидки</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required placeholder="Краткое описание для карточки игры...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Фото</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Наличие</label>
                        <select name="available" class="form-select">
                            <option value="1" @selected(old('available', '1') == '1')>В наличии</option>
                            <option value="0" @selected(old('available') === '0')>Нет в наличии</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-plus me-1"></i> Добавить игру
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" class="admin-toolbar">
            <div class="admin-toolbar__field admin-toolbar__field--search">
                <label class="form-label small text-muted mb-1">Поиск</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Название игры...">
            </div>
            <div class="admin-toolbar__field">
                <label class="form-label small text-muted mb-1">Наличие</label>
                <select name="available" class="form-select">
                    <option value="">Все</option>
                    <option value="1" @selected(request('available') === '1')>В наличии</option>
                    <option value="0" @selected(request('available') === '0')>Нет в наличии</option>
                </select>
            </div>
            <div class="admin-toolbar__actions">
                <button type="submit" class="btn btn-primary-custom">Найти</button>
                <a href="{{ route('admin.products.index', ['add' => 1]) }}#addProductForm" class="btn btn-accent">
                    <i class="fas fa-plus me-1"></i> Новая игра
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 admin-products-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-products-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-photo">Фото</th>
                        <th class="col-product">Игра</th>
                        <th class="col-price">Цены, ₽</th>
                        <th class="col-stock">Наличие</th>
                        <th class="col-actions">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="text-muted">{{ $product->id }}</td>
                        <td>
                            <div class="admin-product-photo">
                                <div class="admin-product-photo__thumb">
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" @if($product->hasWhiteMatteBackground()) class="admin-product-photo__img--matte" @endif>
                                </div>
                                <div class="admin-product-photo__actions">
                                    <form action="{{ route('admin.products.image.store', $product) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="btn btn-sm btn-outline-secondary admin-photo-btn" title="Загрузить фото">
                                            <i class="fas fa-upload"></i>
                                            <input type="file" name="image" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif" required onchange="this.form.submit()">
                                        </label>
                                    </form>
                                    @if($product->hasImageFile())
                                    <form action="{{ route('admin.products.image.destroy', $product) }}" method="POST" onsubmit="return confirm('Удалить фото?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger admin-photo-btn" title="Удалить фото">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="admin-product-info">
                                <div class="admin-product-info__name">{{ $product->name }}</div>
                                <div class="admin-product-info__meta">
                                    <span class="badge rounded-pill text-bg-light border">{{ $product->category->name }}</span>
                                    <span>{{ $product->platform }}</span>
                                    @if($product->hasDiscount())
                                    <span class="badge bg-danger">-{{ $product->discountPercent() }}%</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <form id="update-{{ $product->id }}" action="{{ route('admin.products.update', $product) }}" method="POST" class="d-none">@csrf @method('PUT')</form>
                            <div class="admin-product-prices">
                                <label class="form-label small text-muted mb-0">Со скидкой</label>
                                <input type="number" name="price" form="update-{{ $product->id }}" class="form-control form-control-sm admin-field-price" value="{{ (int) $product->price }}" min="0" step="1">
                                <label class="form-label small text-muted mb-0 mt-1">Было</label>
                                <input type="number" name="old_price" form="update-{{ $product->id }}" class="form-control form-control-sm admin-field-price" value="{{ $product->old_price ? (int) $product->old_price : '' }}" min="0" step="1" placeholder="—">
                                @if($product->hasDiscount())
                                <span class="badge bg-danger mt-1">-{{ $product->discountPercent() }}%</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <select name="available" form="update-{{ $product->id }}" class="form-select form-select-sm admin-field-stock">
                                <option value="1" @selected($product->available)>В наличии</option>
                                <option value="0" @selected(!$product->available)>Нет в наличии</option>
                            </select>
                        </td>
                        <td>
                            <div class="admin-row-actions">
                                <button type="submit" form="update-{{ $product->id }}" class="btn btn-sm btn-accent" title="Сохранить изменения">
                                    <i class="fas fa-save me-1"></i>Сохранить
                                </button>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Удалить игру «{{ $product->name }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить игру">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Игры не найдены</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white border-top">{{ $products->links() }}</div>
    @endif
</div>
@endsection
