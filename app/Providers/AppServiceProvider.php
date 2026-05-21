<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureVercelRuntime();

        Paginator::defaultView('vendor.pagination.playgg');
        Paginator::defaultSimpleView('vendor.pagination.playgg');

        View::composer('shop.layouts.app', function ($view) {
            $cartItemsCount = 0;

            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->with('items')->first();
                $cartItemsCount = $cart?->items->count() ?? 0;
            }

            $view->with('cartItemsCount', $cartItemsCount);
        });
    }

    private function configureVercelRuntime(): void
    {
        if (! env('VERCEL') && ! env('VERCEL_ENV')) {
            return;
        }

        $dbUrl = env('POSTGRES_URL') ?? env('POSTGRES_PRISMA_URL') ?? env('DATABASE_URL');

        if ($dbUrl) {
            Config::set('database.default', 'pgsql');
            Config::set('database.connections.pgsql.url', $dbUrl);
        }

        $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).'/playgg-laravel';

        if (! is_dir($tmp)) {
            @mkdir($tmp, 0755, true);
        }

        Config::set('view.compiled', $tmp.'/views');
        Config::set('session.files', $tmp.'/sessions');
        Config::set('cache.stores.file.path', $tmp.'/cache');
    }
}
