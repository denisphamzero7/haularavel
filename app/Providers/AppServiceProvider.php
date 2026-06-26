<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Modules\Jobs\Events\JobUpdatedEvent;
use App\Modules\Jobs\Listeners\JobActionHandler;
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
        //
        // 2. Đăng ký Event và Listener tại đây
        Event::listen(
            JobUpdatedEvent::class,
            JobActionHandler::class,
        );
    }
}
