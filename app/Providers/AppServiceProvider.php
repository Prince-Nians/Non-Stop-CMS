<?php

namespace App\Providers;

use App\Support\DatabaseFilesystemAdapter;
use App\Support\FrontendRevalidator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
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
        // Storage driver "database": uploads are kept in the shared database; the backend serves them at /media/...
        Storage::extend('database', function ($app, array $config) {
            $adapter = new DatabaseFilesystemAdapter($config['url'] ?? '');

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        FrontendRevalidator::listen();
    }
}
