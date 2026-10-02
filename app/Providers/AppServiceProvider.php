<?php

namespace App\Providers;

use App\Services\StockService;
use App\Services\WorkOrderService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StockService::class);
        $this->app->singleton(WorkOrderService::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
