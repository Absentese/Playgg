<?php

namespace App\Services;

use App\Data\SteamGameCatalog;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SteamCoverService
{
    public function directory(): string
    {
        return public_path('images/products');
    }

    public function resolveAppId(Product $product): ?int
    {
        $bySlug = SteamGameCatalog::appIdsBySlug();

        if (isset($bySlug[$product->slug])) {
            return $bySlug[$product->slug];
        }

        $name = Str::lower($product->name);
        $keys = array_keys(self::legacyNameKeys());
        usort($keys, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        foreach ($keys as $key) {
            if (Str::contains($name, $key)) {
                return self::legacyNameKeys()[$key];
            }
        }

        return null;
    }

    public function downloadForProduct(Product $product, ?int $appId = null): bool
    {
        $appId ??= $this->resolveAppId($product);

        if ($appId === null) {
            return false;
        }

        $dir = $this->directory();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $urls = [
            "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/{$appId}/header.jpg",
            "https://cdn.cloudflare.steamstatic.com/steam/apps/{$appId}/header.jpg",
            "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/{$appId}/capsule_616x353.jpg",
        ];

        foreach ($urls as $url) {
            $response = Http::timeout(30)
                ->withOptions(['verify' => config('app.env') === 'production'])
                ->withHeaders(['User-Agent' => 'playgg/1.0'])
                ->get($url);

            if (! $response->successful() || strlen($response->body()) < 1024) {
                continue;
            }

            $filename = $product->slug.'.jpg';
            $path = $dir.DIRECTORY_SEPARATOR.$filename;

            if ($product->image && $product->image !== $filename) {
                $old = $dir.DIRECTORY_SEPARATOR.$product->image;
                if (is_file($old)) {
                    unlink($old);
                }
            }

            file_put_contents($path, $response->body());
            $product->update(['image' => $filename]);

            return true;
        }

        return false;
    }

    public function downloadAll(): array
    {
        $results = ['ok' => [], 'skip' => [], 'fail' => []];

        foreach (Product::query()->orderBy('id')->get() as $product) {
            if ($this->downloadForProduct($product)) {
                $results['ok'][] = $product->name;

                continue;
            }

            if ($this->resolveAppId($product) === null) {
                $results['skip'][] = $product->name;
            } else {
                $results['fail'][] = $product->name;
            }
        }

        return $results;
    }

    /** @return array<string, int> */
    private static function legacyNameKeys(): array
    {
        return [
            'heroes of might and magic' => 3105440,
            'gothic 1 remake' => 1297900,
            'gothic remake' => 1297900,
            'directive 8020' => 2255370,
            'elden ring' => 2622380,
            'nightreign' => 2622380,
            'metro exodus' => 412020,
            'helldivers 2' => 553850,
            'resident evil 4' => 2050650,
            'kingdom come' => 1771300,
            'dragon\'s dogma 2' => 2054970,
            'dragons dogma 2' => 2054970,
            'hearts of iron' => 394360,
            'stellaris' => 281990,
            'dark souls iii' => 374320,
            'baldur\'s gate 3' => 1086940,
            'cyberpunk 2077' => 1091500,
            'black myth' => 2358720,
            'grand theft auto v' => 271590,
            'red dead redemption 2' => 1174180,
            'hogwarts legacy' => 990080,
            'palworld' => 1623730,
            'witcher 3' => 292030,
            'sekiro' => 814380,
            'hades ii' => 1145350,
            'monster hunter' => 582010,
            'lies of p' => 1627720,
            'space marine 2' => 2183900,
            'counter-strike 2' => 730,
            'dota 2' => 570,
        ];
    }
}
