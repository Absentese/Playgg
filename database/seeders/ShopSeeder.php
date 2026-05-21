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
            Category::create($cat);
        }

        $steam = app(SteamCoverService::class);

        foreach (SteamGameCatalog::games() as $game) {
            $category = Category::where('slug', $game['category'])->first();

            $product = Product::create([
                'category_id' => $category->id,
                'name' => $game['name'],
                'slug' => $game['slug'],
                'price' => $game['price'],
                'old_price' => $game['old_price'] ?? null,
                'platform' => $game['platform'],
                'genre' => $game['genre'],
                'description' => $game['description'],
                'available' => true,
                'is_featured' => $game['is_featured'] ?? false,
                'is_preorder' => $game['is_preorder'] ?? false,
            ]);

            $steam->downloadForProduct($product, $game['steam_app_id']);
        }

        User::create([
            'name' => 'Администратор',
            'email' => 'admin@playgg.ru',
            'password' => 'password',
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'demo',
            'email' => 'demo@playgg.ru',
            'password' => 'password',
        ])->profile()->create([
            'first_name' => 'Демо',
            'last_name' => 'Пользователь',
            'contact_email' => 'demo@playgg.ru',
            'phone' => '+7 (999) 100-20-30',
            'steam_id' => '76561198000000001',
            'steam_profile_url' => 'https://steamcommunity.com/profiles/76561198000000001',
        ]);
    }
}
