<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191); // This is to support MySQL v5.6
        View::composer('*', function($view){
                $view->with('channels', \App\Channel::all());
        });
//        View::share('channels', \App\Channel::all()); //This line does tha same as above. However it runs before our DB migrations, hence doesnt work.
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
