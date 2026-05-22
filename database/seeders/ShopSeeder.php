<?php

namespace Database\Seeders;

use App\Data\SteamGameCatalog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\SteamCoverService;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Экшены', 'slug' => 'action'],
            ['name' => 'RPG', 'slug' => 'rpg'],
            ['name' => 'Стратегии', 'slug' => 'strategy'],
            ['name' => 'Предзаказы', 'slug' => 'preorder'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']],
            );
        }

        $steam = app(SteamCoverService::class);

        foreach (SteamGameCatalog::games() as $game) {
            $category = Category::where('slug', $game['category'])->firstOrFail();

            $product = Product::updateOrCreate(
                ['slug' => $game['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $game['name'],
                    'price' => $game['price'],
                    'old_price' => $game['old_price'] ?? null,
                    'platform' => $game['platform'],
                    'genre' => $game['genre'],
                    'description' => $game['description'],
                    'available' => true,
                    'is_featured' => $game['is_featured'] ?? false,
                    'is_preorder' => $game['is_preorder'] ?? false,
                ],
            );

            if ($this->productNeedsCover($product)) {
                $steam->downloadForProduct($product, $game['steam_app_id']);
            }
        }

        User::updateOrCreate(
            ['email' => 'admin@playgg.ru'],
            [
                'name' => 'Администратор',
                'password' => 'password',
                'is_admin' => true,
            ],
        );

        $demo = User::updateOrCreate(
            ['email' => 'demo@playgg.ru'],
            [
                'name' => 'demo',
                'password' => 'password',
                'is_admin' => false,
            ],
        );

        $demo->profile()->updateOrCreate(
            ['user_id' => $demo->id],
            [
                'first_name' => 'Демо',
                'last_name' => 'Пользователь',
                'contact_email' => 'demo@playgg.ru',
                'phone' => '+7 (999) 100-20-30',
                'steam_id' => '76561198000000001',
                'steam_profile_url' => 'https://steamcommunity.com/profiles/76561198000000001',
            ],
        );
    }

    private function productNeedsCover(Product $product): bool
    {
        if (! $product->image) {
            return true;
        }

        return ! is_file(public_path('images/products/'.$product->image));
    }
}
