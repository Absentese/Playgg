@extends('admin.layouts.app')
@section('title', 'Промокоды')
@section('page_title', 'Промокоды и скидки')

@section('content')
<div class="card admin-card shadow-sm border-0 mb-4">
    <div class="card-header admin-card__header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="fas fa-ticket-alt me-2 text-accent"></i>Новый промокод</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#addPromoForm">
            {{ request()->has('add') || $errors->any() ? 'Свернуть' : 'Развернуть' }}
        </button>
    </div>
    <div class="collapse {{ request()->has('add') || $errors->any() ? 'show' : '' }}" id="addPromoForm">
        <div class="card-body border-top">
            <form action="{{ route('admin.promo-codes.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Код</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" placeholder="PLAYGG10" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Тип</label>
                        <select name="type" class="form-select" required>
                            <option value="percent" @selected(old('type', 'percent') === 'percent')">Процент</option>
                            <option value="fixed" @selected(old('type') === 'fixed')>Сумма ₽</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Значение</label>
                        <input type="number" name="value" class="form-control" value="{{ old('value') }}" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Мин. сумма</label>
                        <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount') }}" min="0" step="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Лимит</label>
                        <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}" min="1" step="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">С (МСК)</label>
                        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                        <div class="form-text">Пусто — сразу активен</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">До (МСК)</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                        <div class="form-text">Пусто — без срока</div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="promo_active_new" checked>
                            <label class="form-check-label" for="promo_active_new">Активен</label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-accent w-100">Создать</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card admin-card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Скидка</th>
                        <th>Мин. сумма</th>
                        <th>Использовано</th>
                        <th>Период</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoCodes as $promo)
                    <tr>
                        <td>
                            <form id="promo-{{ $promo->id }}" action="{{ route('admin.promo-codes.update', $promo) }}" method="POST" class="d-none">@csrf @method('PUT')</form>
                            <input type="text" name="code" form="promo-{{ $promo->id }}" class="form-control form-control-sm font-monospace"
                                   value="{{ $promo->code }}" maxlength="50" required>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <select name="type" form="promo-{{ $promo->id }}" class="form-select form-select-sm" style="max-width:5.5rem">
                                    <option value="percent" @selected($promo->type === 'percent')>%</option>
                                    <option value="fixed" @selected($promo->type === 'fixed')>₽</option>
                                </select>
                                <input type="number" name="value" form="promo-{{ $promo->id }}" class="form-control form-control-sm"
                                       value="{{ $promo->value }}" min="0.01" step="0.01" style="max-width:5rem" required>
                            </div>
                        </td>
                        <td>
                            <input type="number" name="min_order_amount" form="promo-{{ $promo->id }}" class="form-control form-control-sm"
                                   value="{{ $promo->min_order_amount }}" min="0" step="1" placeholder="—" style="max-width:6rem">
                        </td>
                        <td class="text-nowrap small">
                            <div>{{ $promo->used_count }}@if($promo->max_uses) / {{ $promo->max_uses }}@endif</div>
                            <input type="number" name="max_uses" form="promo-{{ $promo->id }}" class="form-control form-control-sm mt-1"
                                   value="{{ $promo->max_uses }}" min="1" placeholder="Лимит" style="max-width:5.5rem">
                        </td>
                        <td>
                            <input type="datetime-local" name="starts_at" form="promo-{{ $promo->id }}" class="form-control form-control-sm mb-1"
                                   value="{{ $promo->starts_at?->format('Y-m-d\TH:i') }}">
                            <input type="datetime-local" name="expires_at" form="promo-{{ $promo->id }}" class="form-control form-control-sm"
                                   value="{{ $promo->expires_at?->format('Y-m-d\TH:i') }}">
                        </td>
                        <td>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" form="promo-{{ $promo->id }}" class="form-check-input"
                                       id="promo_active_{{ $promo->id }}" @checked($promo->is_active)>
                                <label class="form-check-label small" for="promo_active_{{ $promo->id }}">Активен</label>
                            </div>
                        </td>
                        <td>
                            <div class="admin-row-actions">
                                <button type="submit" form="promo-{{ $promo->id }}" class="btn btn-sm btn-accent"><i class="fas fa-save"></i></button>
                                <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST"
                                      onsubmit="return confirm('Удалить промокод «{{ $promo->code }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Промокодов пока нет</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($promoCodes->hasPages())
    <div class="card-footer admin-card__footer">{{ $promoCodes->links() }}</div>
    @endif
</div>
@endsection


