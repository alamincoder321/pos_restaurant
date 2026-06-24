<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\CompanyProfile;
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
        $data['company'] = CompanyProfile::first();
        $data['branches'] = Branch::latest()->get();
        view()->share($data);
    }
}
