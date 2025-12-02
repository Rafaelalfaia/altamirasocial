<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Cidadao;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    View::composer('*', function ($view) {
        if (auth()->check() && auth()->user()->hasRole('Cidadao')) {
            $view->with('cidadao', Cidadao::where('user_id', auth()->id())->first());
        }
    });
}
}
