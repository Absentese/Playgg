<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SteamId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = preg_replace('/\D/', '', (string) $value);

        if (strlen($id) !== 17 || ! str_starts_with($id, '7656')) {
            $fail('Укажите корректный Steam ID — 17 цифр из раздела «Об аккаунте» в Steam.');
        }
    }
}
