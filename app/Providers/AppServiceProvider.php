<?php

namespace App\Providers;

use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $shop = collect([
            'name' => env('APP_NAME'),
            'description' => 'The next Genration online shop Apps'
        ]); 

        try {
            if(DB::connection()->getPDO()) {
                $shop = Cache::rememberForever('shop', function () {
                    return Store::first();
                });
            }
        } catch (\Throwable $th) {
            //throw $th;
        }


        View::macro('vue', function ($page = []) use ($shop) {
            return view('app', [
                'jsapp' => [
                    'page' => $page,
                    'shop' => $shop
                ]
            ]);
        });

        Relation::enforceMorphMap([
            'post' => 'App\Models\Post',
            'Product' => 'App\Models\Product',
            'User' => 'App\Models\User',
            'Category' => 'App\Models\Category',
        ]);
    }
}
