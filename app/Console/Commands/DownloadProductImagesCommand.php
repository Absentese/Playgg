<?php

namespace App\Console\Commands;

use App\Services\SteamCoverService;
use Illuminate\Console\Command;

class DownloadProductImagesCommand extends Command
{
    protected $signature = 'products:download-images';

    protected $description = 'Загрузить обложки игр из Steam CDN';

    public function handle(SteamCoverService $steam): int
    {
        $this->info('Загрузка обложек...');

        $results = $steam->downloadAll();

        foreach ($results['ok'] as $name) {
            $this->line("  <fg=green>✓</> {$name}");
        }

        foreach ($results['fail'] as $name) {
            $this->line("  <fg=red>✗</> {$name}");
        }

        foreach ($results['skip'] as $name) {
            $this->line("  <fg=yellow>?</> {$name} (нет App ID)");
        }

        $this->newLine();
        $this->info('Готово: '.count($results['ok']).' из '.array_sum(array_map('count', $results)));

        return count($results['fail']) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
