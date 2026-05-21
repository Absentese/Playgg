@extends('shop.layouts.app')
@section('title', 'Редактирование профиля')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm p-4 profile-edit-card">
                <h3 class="mb-4">Редактировать профиль</h3>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <p class="shop-form-card__hint mb-3">Эти данные подставляются при оплате в корзине.</p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Имя</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $profile->first_name ?? $checkoutDefaults['first_name']) }}"
                                   placeholder="Иван">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Фамилия</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $profile->last_name ?? $checkoutDefaults['last_name']) }}"
                                   placeholder="Иванов">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email для чека</label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                               value="{{ old('contact_email', $profile->contact_email ?? $checkoutDefaults['email']) }}"
                               placeholder="email@example.com">
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">На этот адрес придёт чек и уведомления о заказе</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               data-phone-mask
                               value="{{ old('phone', $profile->formattedPhone()) }}"
                               placeholder="+7 (999) 123-45-67">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Для связи по заказам, необязательно</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="profile_steam_id">Steam ID</label>
                        <input type="text"
                               id="profile_steam_id"
                               name="steam_id"
                               class="form-control @error('steam_id') is-invalid @enderror"
                               value="{{ old('steam_id', $profile->steam_id) }}"
                               inputmode="numeric"
                               maxlength="17"
                               placeholder="76561198000000000">
                        @error('steam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">17 цифр из Steam → «Об аккаунте». Сохранится для быстрого оформления заказов</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="profile_steam_url">Ссылка на профиль Steam</label>
                        <input type="url"
                               id="profile_steam_url"
                               name="steam_profile_url"
                               class="form-control @error('steam_profile_url') is-invalid @enderror"
                               value="{{ old('steam_profile_url', $profile->steam_profile_url) }}"
                               placeholder="https://steamcommunity.com/id/...">
                        @error('steam_profile_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Необязательно — для проверки аккаунта</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Фото профиля</label>
                        <div class="profile-avatar-block mb-3">
                            <img src="{{ $profile->avatarUrl() }}" class="profile-avatar rounded-circle" width="96" height="96" alt="">
                            <p class="profile-avatar-caption">Текущее фото</p>
                        </div>
                        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/jpeg,image/png,image/webp,image/gif">
                        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">JPG, PNG, WebP или GIF, до 4 МБ</div>
                    </div>
                    <button type="submit" class="btn btn-accent">Сохранить</button>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary">Отмена</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const profileSteamId = document.getElementById('profile_steam_id');
if (profileSteamId) {
    profileSteamId.addEventListener('input', () => {
        profileSteamId.value = profileSteamId.value.replace(/\D/g, '').slice(0, 17);
    });
}
</script>
@endpush
