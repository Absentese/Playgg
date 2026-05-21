<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Rules\SteamId;
use App\Support\PhoneFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('profile');
        $orders = $user->orders()->latest()->get();

        return view('shop.profile', compact('user', 'orders'));
    }

    public function edit()
    {
        $user = auth()->user()->load('profile');
        $profile = $user->profile ?? $user->profile()->create([]);
        $checkoutDefaults = Profile::checkoutDefaultsFor($user);

        return view('shop.edit_profile', compact('profile', 'checkoutDefaults'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'steam_id' => ['nullable', 'string', 'max:17', new SteamId],
            'steam_profile_url' => 'nullable|string|max:255|url',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:4096',
        ]);

        if (! empty($data['steam_id'])) {
            $data['steam_id'] = preg_replace('/\D/', '', $data['steam_id']);
        } else {
            $data['steam_id'] = null;
        }

        $data['phone'] = PhoneFormatter::normalize($data['phone'] ?? null);

        $profile = auth()->user()->profile ?? auth()->user()->profile()->create([]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = auth()->id().'_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $directory = public_path('images/avatars');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $file->move($directory, $filename);
            $profile->deleteAvatarFile();
            $data['avatar'] = $filename;
        }

        $profile->update($data);

        return redirect()->route('profile')->with('success', 'Профиль обновлён');
    }
}
