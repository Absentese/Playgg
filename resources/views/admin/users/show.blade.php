@extends('admin.layouts.app')
@section('title', $user->name)
@section('page_title', 'Пользователь: '.$user->name)

@section('content')
@php($profile = $user->profile)
@php($checkout = \App\Models\Profile::checkoutDefaultsFor($user))

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Редактирование аккаунта</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Отображаемое имя</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email входа</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_admin" value="0">
                        <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="user_is_admin"
                               @checked(old('is_admin', $user->is_admin))>
                        <label class="form-check-label" for="user_is_admin">Права администратора</label>
                        @if($user->id === auth()->id())
                        <div class="form-text">Свои права админа снять нельзя.</div>
                        @endif
                    </div>

                    <hr>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Имя (заказы)</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $profile?->first_name ?? $checkout['first_name']) }}">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Фамилия</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $profile?->last_name ?? $checkout['last_name']) }}">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email для чека</label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                               value="{{ old('contact_email', $profile?->contact_email ?? $checkout['email']) }}">
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               data-phone-mask
                               value="{{ old('phone', $profile?->formattedPhone()) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin_steam_id">Steam ID</label>
                        <input type="text" id="admin_steam_id" name="steam_id"
                               class="form-control @error('steam_id') is-invalid @enderror"
                               value="{{ old('steam_id', $profile?->steam_id) }}"
                               inputmode="numeric" maxlength="17" placeholder="76561198000000000">
                        @error('steam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ссылка на профиль Steam</label>
                        <input type="url" name="steam_profile_url" class="form-control @error('steam_profile_url') is-invalid @enderror"
                               value="{{ old('steam_profile_url', $profile?->steam_profile_url) }}"
                               placeholder="https://steamcommunity.com/profiles/...">
                        @error('steam_profile_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <p class="small text-muted mb-3">Зарегистрирован: {{ $user->created_at->format('d.m.Y H:i') }}</p>

                    <button type="submit" class="btn btn-accent w-100">Сохранить</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 mt-2">К списку</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Заказы пользователя</h5>
                <span class="badge bg-primary">{{ $user->orders->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($user->orders->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>№</th>
                                <th>Дата</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Оплата</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders as $order)
                            <tr>
                                <td>{{ $order->numberLabel() }}</td>
                                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ number_format($order->totalCost(), 0, ',', ' ') }} ₽</td>
                                <td><span class="badge status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                                <td>{{ $order->paid ? 'Оплачен' : 'Не оплачен' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Открыть</a>
                                    @include('admin.partials.order-delete-form', ['order' => $order])
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4 mb-0">Заказов нет</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const adminSteamId = document.getElementById('admin_steam_id');
if (adminSteamId) {
    adminSteamId.addEventListener('input', () => {
        adminSteamId.value = adminSteamId.value.replace(/\D/g, '').slice(0, 17);
    });
}
</script>
@endpush
