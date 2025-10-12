<?php

namespace App\Providers;

use App\Http\Controllers\Admin\BrandingController;
use App\Models\BrandingSetting;
use Illuminate\Support\Facades\Schema;
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

            // Check if branding_settings table exists before querying
            if (\Schema::hasTable('branding_settings')) {
                // Use fresh query to ensure we get the latest data
                $settings = BrandingSetting::where('is_active', true)->orderBy('key')->get();

                foreach ($settings as $setting) {
                    $brandingSettings[$setting->key] = $setting->value;
                }
            }

            return $brandingSettings;
        });

        // Share branding settings with all Blade views
        $brandingSettings = [];

        // Check if branding_settings table exists before querying
        if (\Schema::hasTable('branding_settings')) {
            // Use fresh query to ensure we get the latest data
            $settings = BrandingSetting::where('is_active', true)->orderBy('key')->get();

            foreach ($settings as $setting) {
                $brandingSettings[$setting->key] = $setting->value;
            }
        }

        view()->share('brandingSettings', $brandingSettings);
    }
}
