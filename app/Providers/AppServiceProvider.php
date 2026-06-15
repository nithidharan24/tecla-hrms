<?php

namespace App\Providers;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
   View::share('generalSettings', GeneralSetting::first());
View::share('logoSetting', LogoSetting::first());
        
    }
}
