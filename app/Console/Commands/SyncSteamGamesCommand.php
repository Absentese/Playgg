<?php

namespace App\Console\Commands;

use App\Data\SteamGameCatalog;
use App\Models\Category;
use App\Models\Product;
use App\Services\SteamCoverService;
use Illuminate\Console\Command;

class SyncSteamGamesCommand extends Command
{
    protected $signature = 'shop:sync-games {--images : Перезагрузить обложки из Steam}';

    protected $description = 'Синхронизировать каталог игр и обновить обложки Steam';

    public function handle(SteamCoverService $steam): int
    {
        $created = 0;
        $updated = 0;
        $catalogSlugs = [];

        foreach (SteamGameCatalog::games() as $game) {
            $catalogSlugs[] = $game['slug'];
            $category = Category::where('slug', $game['category'])->first();

            if (! $category) {
                $this->warn("Категория «{$game['category']}» не найдена, пропуск: {$game['name']}");

                continue;
            }

            $payload = [
                'category_id' => $category->id,
                'name' => $game['name'],
                'platform' => $game['platform'],
                'genre' => $game['genre'],
                'price' => $game['price'],
                'old_price' => $game['old_price'] ?? null,
                'description' => $game['description'],
                'available' => true,
                'is_featured' => $game['is_featured'] ?? false,
                'is_preorder' => $game['is_preorder'] ?? false,
            ];

            $product = Product::query()->where('slug', $game['slug'])->first();
            $wasExisting = $product !== null;

            $product = Product::updateOrCreate(
                ['slug' => $game['slug']],
                $payload
            );

            if ($wasExisting) {
                $updated++;
            } else {
                $created++;
            }

            if ($this->option('images')) {
                if ($steam->downloadForProduct($product, $game['steam_app_id'])) {
                    $this->line("  <fg=green>✓</> {$game['name']}");
                } else {
                    $this->line("  <fg=red>✗</> {$game['name']} (обложка)");
                }
            }
        }

        $removed = Product::query()->whereNotIn('slug', $catalogSlugs)->delete();

        $this->newLine();
        $this->info("Каталог: добавлено {$created}, обновлено {$updated}, удалено дубликатов {$removed}.");

        if (! $this->option('images')) {
            $this->comment('Для загрузки обложек запустите: php artisan shop:sync-games --images');
        }

        return self::SUCCESS;
    }
}
