<?php

namespace App\Providers;

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
        // SQLite: WAL يسمح بالقراءة أثناء الكتابة، وbusy_timeout ينتظر بدل خطأ "database is locked"
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') !== ':memory:') {
            try {
                \Illuminate\Support\Facades\DB::statement('PRAGMA journal_mode=WAL');
                \Illuminate\Support\Facades\DB::statement('PRAGMA busy_timeout=8000');
                \Illuminate\Support\Facades\DB::statement('PRAGMA synchronous=NORMAL');
            } catch (\Throwable $e) {
            }
        }
    }
}
