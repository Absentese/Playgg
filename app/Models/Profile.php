<?php

namespace App\Models;

use App\Support\PhoneFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'contact_email',
        'phone',
        'steam_id',
        'steam_profile_url',
        'avatar',
    ];

    public static function checkoutDefaultsFor(User $user): array
    {
        $profile = $user->profile;
        $nameParts = self::splitName($user->name);

        return [
            'first_name' => $profile?->first_name ?: $nameParts['first_name'],
            'last_name' => $profile?->last_name ?: $nameParts['last_name'],
            'email' => $profile?->contact_email ?: $user->email,
            'steam_id' => $profile?->steam_id ?? '',
            'steam_profile_url' => $profile?->steam_profile_url ?? '',
        ];
    }

    /** @return array{first_name: string, last_name: string} */
    public static function splitName(string $name): array
    {
        $parts = preg_split('/\s+/u', trim($name), 2);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    public function syncCheckoutData(array $data): void
    {
        $this->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'contact_email' => $data['email'],
            'steam_profile_url' => $data['steam_profile_url'] ?? null,
        ]);
    }

    public function steamCommunityUrl(): ?string
    {
        if ($this->steam_profile_url) {
            return $this->steam_profile_url;
        }

        if ($this->steam_id) {
            return 'https://steamcommunity.com/profiles/'.$this->steam_id;
        }

        return null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formattedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        return PhoneFormatter::format($this->phone);
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => PhoneFormatter::normalize($value),
        );
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            $publicPath = public_path('images/avatars/'.$this->avatar);
            if (is_file($publicPath)) {
                return asset('images/avatars/'.$this->avatar).'?v='.filemtime($publicPath);
            }

            $legacyPath = public_path('storage/'.$this->avatar);
            if (is_file($legacyPath)) {
                return asset('storage/'.$this->avatar);
            }
        }

        return asset('images/avatar-default.svg');
    }

    public function deleteAvatarFile(): void
    {
        if (! $this->avatar) {
            return;
        }

        $publicPath = public_path('images/avatars/'.$this->avatar);
        if (is_file($publicPath)) {
            @unlink($publicPath);

            return;
        }

        $legacyPath = public_path('storage/'.$this->avatar);
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }
}
