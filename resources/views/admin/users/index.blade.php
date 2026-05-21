@extends('admin.layouts.app')
@section('title', 'Пользователи')
@section('page_title', 'Пользователи')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small text-muted">Поиск по имени или email</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Имя, email...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary-custom">Найти</button>
                @if(request('q'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Сброс</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Заказов</th>
                        <th>Регистрация</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            {{ $user->name }}
                            @if($user->is_admin)
                            <span class="badge bg-dark ms-1">Админ</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->profile?->formattedPhone() ?? '—' }}</td>
                        <td>{{ $user->orders_count }}</td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">Управление</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Пользователи не найдены</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>
@endsection
