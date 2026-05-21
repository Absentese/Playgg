<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use App\Models\Profile;
use App\Support\PhoneFormatter;
use Illuminate\Console\Command;

class NormalizeStoredPhones extends Command
{
    protected $signature = 'app:normalize-phones';

    protected $description = 'Привести сохранённые телефоны к формату +7 (999) 123-45-67';

    public function handle(): int
    {
        $profiles = 0;
        Profile::query()->whereNotNull('phone')->where('phone', '!=', '')->each(function (Profile $profile) use (&$profiles) {
            $normalized = PhoneFormatter::normalize($profile->getRawOriginal('phone'));
            if ($normalized !== $profile->getRawOriginal('phone')) {
                $profile->update(['phone' => $normalized]);
                $profiles++;
            }
        });

        $messages = 0;
        ContactMessage::query()->whereNotNull('phone')->where('phone', '!=', '')->each(function (ContactMessage $message) use (&$messages) {
            $normalized = PhoneFormatter::normalize($message->phone);
            if ($normalized !== $message->phone) {
                $message->update(['phone' => $normalized]);
                $messages++;
            }
        });

        $this->info("Обновлено профилей: {$profiles}, сообщений: {$messages}.");

        return self::SUCCESS;
    }
}
