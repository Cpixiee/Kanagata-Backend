<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Logsheet;
use App\Models\Ledger;
use App\Observers\LogsheetObserver;
use App\Observers\LedgerObserver;

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
        Logsheet::observe(LogsheetObserver::class);
        Ledger::observe(LedgerObserver::class);
    }
}
