<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
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
        $this->configureRailway();

        Paginator::defaultView('vendor.pagination.playgg');
        Paginator::defaultSimpleView('vendor.pagination.playgg');

        View::composer('shop.layouts.app', function ($view): void {
            $cartItemsCount = 0;

            if (Auth::check()) {
                $cartItemsCount = (int) Cart::query()
                    ->where('user_id', Auth::id())
                    ->withCount('items')
                    ->value('items_count');
            }

            $view->with('cartItemsCount', $cartItemsCount);
        });
    }

    private function configureRailway(): void
    {
        if (! env('RAILWAY_ENVIRONMENT') && ! env('RAILWAY_PUBLIC_DOMAIN')) {
            return;
        }

        if ($databaseUrl = env('DATABASE_URL')) {
            Config::set('database.default', 'pgsql');
            Config::set('database.connections.pgsql.url', $databaseUrl);
        }

        if ($domain = env('RAILWAY_PUBLIC_DOMAIN')) {
            URL::forceRootUrl('https://'.$domain);
            URL::forceScheme('https');
        }
    }
}
