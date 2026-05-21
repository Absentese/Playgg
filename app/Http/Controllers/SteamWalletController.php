<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\SteamTopup;
use Illuminate\Http\Request;

class SteamWalletController extends Controller
{
    public function show()
    {
        $checkout = auth()->check()
            ? Profile::checkoutDefaultsFor(auth()->user()->load('profile'))
            : null;

        return view('shop.services.steam-wallet', [
            'presetAmounts' => [100, 300, 500, 1000, 2000, 5000],
            'checkout' => $checkout,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'steam_id' => ['required', 'string', 'regex:/^\d{17}$/'],
            'amount' => ['required', 'numeric', 'min:100', 'max:15000'],
            'email' => ['required', 'email'],
        ], [
            'steam_id.required' => 'Укажите Steam ID (17 цифр).',
            'steam_id.regex' => 'Steam ID должен содержать ровно 17 цифр.',
            'amount.min' => 'Минимальная сумма пополнения — 100 ₽.',
            'amount.max' => 'Максимальная сумма за один платёж — 15 000 ₽.',
        ]);

        $profile = auth()->user()->profile ?? auth()->user()->profile()->create([]);
        $profile->update([
            'steam_id' => $data['steam_id'],
            'contact_email' => $data['email'],
        ]);

        $topup = SteamTopup::create([
            'user_id' => auth()->id(),
            'steam_id' => $data['steam_id'],
            'amount' => $data['amount'],
            'email' => $data['email'],
            'status' => 'pending',
            'paid' => false,
        ]);

        return redirect()
            ->route('services.steam-wallet.payment', $topup)
            ->with('success', 'Заявка создана. Завершите оплату.');
    }

    public function payment(SteamTopup $topup)
    {
        abort_unless($topup->user_id === auth()->id(), 403);
        abort_if($topup->paid, 403);

        return view('shop.services.steam-wallet-payment', compact('topup'));
    }

    public function processPayment(Request $request, SteamTopup $topup)
    {
        abort_unless($topup->user_id === auth()->id(), 403);

        $request->validate([
            'card_number' => 'required|string',
            'card_name' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
        ]);

        $topup->update([
            'paid' => true,
            'status' => 'processing',
        ]);

        return redirect()
            ->route('services.steam-wallet')
            ->with('success', "Оплата принята! Средства будут зачислены на Steam ID {$topup->formattedSteamId()} в течение 1–5 минут.");
    }
}
