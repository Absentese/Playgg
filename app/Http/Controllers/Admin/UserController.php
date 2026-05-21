<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\SteamId;
use App\Support\PhoneFormatter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profile')->withCount('orders');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['profile', 'orders' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin' => 'nullable|boolean',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'steam_id' => ['nullable', 'string', 'max:17', new SteamId],
            'steam_profile_url' => 'nullable|string|max:255|url',
        ]);

        if ($user->id === auth()->id() && ! $request->boolean('is_admin')) {
            return back()->withInput()->with('error', 'Нельзя снять права администратора со своего аккаунта.');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        $profileData = [
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'phone' => PhoneFormatter::normalize($data['phone'] ?? null),
            'steam_id' => ! empty($data['steam_id']) ? preg_replace('/\D/', '', $data['steam_id']) : null,
            'steam_profile_url' => $data['steam_profile_url'] ?? null,
        ];

        $profile = $user->profile ?? $user->profile()->create([]);
        $profile->update($profileData);

        return back()->with('success', 'Данные пользователя сохранены.');
    }
}
