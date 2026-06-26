<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
   protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            // Mở khóa hoàn toàn cho môi trường local, không cần check đăng nhập hay email
            if (app()->environment('local')) {
                return true;
            }

            // Cấu hình cho môi trường chạy thật (Production) sau này
            return in_array(optional($user)->email, [
                'admin@quandh.com', // Thay bằng email admin của bạn nếu cần
                'dev.lead@mycompany.com', // Thêm email của người khác vào đây

            ]);
        });
    }

    protected function authorization(): void
    {
        $this->gate();

        Horizon::auth(function ($request) {
            if (app()->environment('local')) {
                return true;
            }

            return Gate::check('viewHorizon', [$request->user()]);
        });
    }
}
