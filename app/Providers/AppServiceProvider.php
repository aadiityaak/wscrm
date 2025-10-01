<?php

namespace App\Providers;

use App\Http\Controllers\Admin\BrandingController;
use App\Models\BrandingSetting;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
    public function boot(): void
    {
        // Share branding settings with all Inertia views
        Inertia::share('brandingSettings', function () {
            $brandingSettings = [];
            $settings = BrandingSetting::getAllActive();

            foreach ($settings as $setting) {
                $brandingSettings[$setting->key] = $setting->value;
            }

            return $brandingSettings;
        });
    }
}
