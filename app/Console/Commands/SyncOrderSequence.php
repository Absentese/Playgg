<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOrderSequence extends Command
{
    protected $signature = 'app:sync-order-sequence';

    protected $description = 'Синхронизировать счётчик id заказов (SQLite) с фактическими записями';

    public function handle(): int
    {
        if (config('database.default') !== 'sqlite') {
            $this->warn('Команда только для SQLite.');

            return self::SUCCESS;
        }

        $maxId = Order::max('id') ?? 0;

        if ($maxId === 0) {
            DB::statement("DELETE FROM sqlite_sequence WHERE name = 'orders'");
            $this->info('Счётчик сброшен. Следующий заказ получит id 1.');
        } else {
            DB::statement('UPDATE sqlite_sequence SET seq = ? WHERE name = ?', [$maxId, 'orders']);
            $this->info("Счётчик установлен на {$maxId}. Следующий заказ получит id ".($maxId + 1).'.');
        }

        return self::SUCCESS;
    }
}
