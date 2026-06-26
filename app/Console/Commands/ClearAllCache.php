<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-all-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa toàn bộ cache của ứng dụng (application, config, route, view)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu xóa cache định kỳ...');

        Artisan::call('cache:clear');
        $this->line('Application cache đã được xóa!');

        Artisan::call('config:clear');
        $this->line('Configuration cache đã được xóa!');

        Artisan::call('route:clear');
        $this->line('Route cache đã được xóa!');

        Artisan::call('view:clear');
        $this->line('View cache đã được xóa!');

        $this->info('Hoàn tất! Toàn bộ cache đã được xóa.');

        return Command::SUCCESS;
    }
}
