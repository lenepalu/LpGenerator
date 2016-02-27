<?php

namespace LenePalu\LpGenerator;

use Illuminate\Support\ServiceProvider;

class LpGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lp-generator.php' => config_path('lp-generator.php'),
        ]);

        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/lp-generator/'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'LenePalu\LpGenerator\Commands\LpCommand',
            'LenePalu\LpGenerator\Commands\LpControllerCommand',
            'LenePalu\LpGenerator\Commands\LpModelCommand',
            'LenePalu\LpGenerator\Commands\LpMigrationCommand',
            'LenePalu\LpGenerator\Commands\LpViewCommand'
        );
    }

}
