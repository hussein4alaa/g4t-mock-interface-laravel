<?php

namespace G4T\MockInterface;

use G4T\MockInterface\Commands\CreateInterfaceFile;
use G4T\MockInterface\Commands\CreateSchemaFile;
use Illuminate\Support\ServiceProvider;

class MockServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config/interfaces.php' => base_path('config/interfaces.php'),
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateSchemaFile::class,
                CreateInterfaceFile::class,
            ]);
        }
        $this->loadRoutesFrom(__DIR__.'/Routes/routes.php');
    }
}
