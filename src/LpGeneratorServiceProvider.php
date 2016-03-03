<?php

/**
 * Created by PhpStorm.
 * User: LenePalu
 * Date: 2/29/16
 * Time: 20:45
 */
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
     * Perform posts-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lp-generator.php' => config_path('lp-generator.php'),
        ]);

//        $this->publishes([
//            __DIR__ . '/stubs/' => base_path('resources/lp-generator/'),
//        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'LenePalu\LpGenerator\Commands\LpCrudMakeCommand',
            'LenePalu\LpGenerator\Commands\LpControllerMakeCommand',
            'LenePalu\LpGenerator\Commands\LpRequestMakeCommand',
            'LenePalu\LpGenerator\Commands\LpModelMakeCommand',
            'LenePalu\LpGenerator\Commands\LpMigrateMakeCommand',
            'LenePalu\LpGenerator\Commands\LpViewMakeCommand'
        );
    }

}
