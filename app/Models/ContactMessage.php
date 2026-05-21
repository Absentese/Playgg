<?php

namespace App\Models;

use App\Support\PhoneFormatter;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'is_processed'];

    protected function casts(): array
    {
        return ['is_processed' => 'boolean'];
    }

    public function formattedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        return PhoneFormatter::format($this->phone);
    }
}
