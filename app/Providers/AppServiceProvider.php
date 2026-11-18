<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\SettingRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /** اتصال اینترفیس‌های لایه‌ی Repository به پیاده‌سازی Eloquent */
    public array $bindings = [
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        SettingRepositoryInterface::class => SettingRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // در محیط توسعه، دسترسی به ویژگی‌های تعریف‌نشده و lazy loading خطا می‌دهد
        Model::shouldBeStrict(! $this->app->isProduction());

        // سیاست رمز عبور: حداقل ۸ کاراکتر، حروف بزرگ و کوچک و عدد
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

        RateLimiter::for('register', fn (Request $request) => Limit::perHour(5)->by($request->ip()));
        // نام شرکت و لوگو برای قالب‌های ورود و پنل مدیریت
        View::composer(['layouts.auth', 'layouts.admin'], function ($view) {
            $settings = app(SettingService::class);
            $view->with([
                'siteSettings' => $settings->all(),
                'siteLogo' => $settings->logoSrc(),
            ]);
        });

        RateLimiter::for('admin', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
    }
}
