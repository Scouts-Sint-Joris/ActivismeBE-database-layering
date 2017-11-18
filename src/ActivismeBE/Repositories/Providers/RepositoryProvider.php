<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Providers;

use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators\CriteriaCreator;
use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators\RepositoryCreator;
use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\MakeCriteriaCommand;
use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\MakeRepositoryCommand;

use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

/**
 * Class RepositoryProvider
 *
 * @package ActivismeBE\DatabaseLayering\Providers
 */
/**
 * Class RepositoryProvider
 *
 * @package Bosnadev\Repositories\Providers
 */
class RepositoryProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config path.
        $config_path = __DIR__ . '/../../../../config/repositories.php';
        
        // Publish config.
        $this->publishes([$config_path => config_path('repositories.php')], 'repositories');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register bindings.
        $this->registerBindings();

        // Register make repository command.
        $this->registerMakeRepositoryCommand();

        // Register make criteria command.
        $this->registerMakeCriteriaCommand();

        // Register commands
        $this->commands(['command.repository.make', 'command.criteria.make']);

        // Config path.
        $config_path = __DIR__ . '/../../../../config/repositories.php';

        // Merge config.
        $this->mergeConfigFrom($config_path, 'repositories');
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings()
    {
        // FileSystem.
        $this->app->instance('FileSystem', new Filesystem());

        // Composer.
        $this->app->bind('Composer', function ($app) { 
            return new Composer($app['FileSystem']);
        });

        // Repository creator.
        $this->app->singleton('RepositoryCreator', function ($app) {
            return new RepositoryCreator($app['FileSystem']);
        });

        // Criteria creator.
        $this->app->singleton('CriteriaCreator', function ($app) {
            return new CriteriaCreator($app['FileSystem']);
        });
    }

    /**
     * Register the make:repository command.
     *
     * @return void
     */
    protected function registerMakeRepositoryCommand()
    {
        // Make repository command.
        $this->app->singleton('command.repository.make', function ($app) {
            return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
        });
    }

    /**
     * Register the make:criteria command.
     *
     * @return void
     */
    protected function registerMakeCriteriaCommand()
    {
        // Make criteria command.
        $this->app->singleton('command.criteria.make', function ($app) {
            return new MakeCriteriaCommand($app['CriteriaCreator'], $app['Composer']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.repository.make', 'command.criteria.make'];
    }
}