@extends('shop.layouts.app')
@section('title', 'Профиль')
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center p-4 shadow-sm">
                <img src="{{ $user->profile?->avatarUrl() ?? asset('images/avatar-default.svg') }}" class="profile-avatar rounded-circle mx-auto mb-3" width="120" height="120" alt="{{ $user->name }}">
                <h4>{{ $user->name }}</h4>
                <p class="text-muted small">С {{ $user->created_at->format('d.m.Y') }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">Редактировать</a>
            </div>
            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6>Контакты</h6>
                    @php($checkout = \App\Models\Profile::checkoutDefaultsFor($user))
                    <p class="mb-1 small"><i class="fas fa-user me-2"></i>{{ trim($checkout['first_name'].' '.$checkout['last_name']) ?: '—' }}</p>
                    <p class="mb-1 small"><i class="fas fa-envelope me-2"></i>{{ $checkout['email'] }}</p>
                    <p class="mb-1 small text-clear"><i class="fas fa-phone me-2"></i>{{ $user->profile?->formattedPhone() ?? '—' }}</p>
                    <p class="mb-1 small"><i class="fab fa-steam me-2"></i>
                        @if($user->profile?->steam_id)
                        <span class="font-monospace">{{ $user->profile->steam_id }}</span>
                        @else
                        Steam ID не указан
                        @endif
                    </p>
                    @if($user->profile?->steamCommunityUrl())
                    <p class="mb-0 small"><i class="fas fa-link me-2"></i><a href="{{ $user->profile->steamCommunityUrl() }}" target="_blank" rel="noopener" class="text-break">Профиль Steam</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: var(--primary);">Мои заказы</div>
                <div class="card-body">
                    @if($orders->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover shop-orders-table">
                            <thead><tr><th>№</th><th>Дата</th><th>Сумма</th><th>Статус</th><th></th></tr></thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->numberLabel() }}</td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ number_format($order->totalCost(), 0, ',', ' ') }} ₽</td>
                                    <td><span class="order-status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                                    <td><a href="{{ route('order.show', $order) }}" class="btn btn-sm btn-primary-custom">Подробнее</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="mb-0">Заказов пока нет. <a href="{{ route('products') }}">Перейти в каталог</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
