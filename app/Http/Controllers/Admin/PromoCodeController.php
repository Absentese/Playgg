<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::query()
            ->orderByDesc('is_active')
            ->orderBy('code')
            ->paginate(20);

        return view('admin.promo_codes.index', compact('promoCodes'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['code'] = PromoCode::normalizeCode($data['code']);

        PromoCode::create($data);

        return back()->with('success', 'Промокод создан.');
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $data = $this->validated($request, $promoCode);
        $data['code'] = PromoCode::normalizeCode($data['code']);

        $promoCode->update($data);

        return back()->with('success', 'Промокод обновлён.');
    }

    public function destroy(PromoCode $promoCode)
    {
        if ($promoCode->orders()->exists()) {
            return back()->with('error', 'Нельзя удалить промокод: он уже использовался в заказах.');
        }

        $promoCode->delete();

        return back()->with('success', 'Промокод удалён.');
    }

    private function validated(Request $request, ?PromoCode $promoCode = null): array
    {
        $codeRule = Rule::unique('promo_codes', 'code');
        if ($promoCode) {
            $codeRule->ignore($promoCode->id);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', $codeRule],
            'type' => ['required', Rule::in([PromoCode::TYPE_PERCENT, PromoCode::TYPE_FIXED])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['min_order_amount'] = $data['min_order_amount'] ?? null;
        $data['max_uses'] = $data['max_uses'] ?? null;

        if ($data['type'] === PromoCode::TYPE_PERCENT && $data['value'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'value' => 'Процент скидки не может быть больше 100.',
            ]);
        }

        return $this->normalizeSchedule($data);
    }

    private function normalizeSchedule(array $data): array
    {
        foreach (['starts_at', 'expires_at'] as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;

                continue;
            }

            $data[$field] = Carbon::parse($data[$field], config('app.timezone'));
        }

        return $data;
    }
}
