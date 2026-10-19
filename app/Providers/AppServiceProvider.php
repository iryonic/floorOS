<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        try {
            $sessionLifetime = (int) Setting::get('session_lifetime', 10080); // Default to 1 week (10,080 minutes)
            if ($sessionLifetime > 0) {
                config(['session.lifetime' => $sessionLifetime]);
            }
            if (Setting::get('session_expire_on_close', '0') === '1') {
                config(['session.expire_on_close' => true]);
            }
        } catch (\Throwable $e) {
            // Graceful fallback during migrations or setup
        }

        View::composer('*', function ($view) {
            $view->with('themeCss', Setting::getThemeCssVariables());
            $view->with('storeName', Setting::get('store_name', 'FloorOS Gallery'));
            $view->with('storeTagline', Setting::get('store_tagline', 'Flooring Store Management'));
            $view->with('storePhone', Setting::get('store_phone', '+91 98450 12345'));
            $view->with('storeEmail', Setting::get('store_email', 'hello@flooros.gallery'));
            $view->with('storeAddress', Setting::get('store_address', 'Indiranagar, Bengaluru'));
            $view->with('currencySymbol', Setting::get('currency_symbol', '₹'));
            $view->with('unreadEnquiriesCount', Enquiry::where('status', 'new')->count());
        });
    }
}
