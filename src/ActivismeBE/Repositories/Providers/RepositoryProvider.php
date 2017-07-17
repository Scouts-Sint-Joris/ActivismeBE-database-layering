<?php

namespace ActivismeBE\DatabaseLayering\Providers;

use Illuminate\Support\{Composer, ServiceProvider};
use Illuminate\Filesystem\Filesystem;

use ActivismeBE\DatabaseLayering\Console\Commands\MakeCriteriaCommand;
use ActivismeBE\DatabaseLayering\Console\Commands\MakeRepositoryCommand;
use ActivismeBE\DatabaseLayering\Console\Commands\Creators\CriteriaCreator;
use ActivismeBE\DatabaseLayering\Console\Commands\Creators\RepositoryCreator;

/**
 * Class RepositoryProvider
 *
 * @package ActivismeBE\DatabaseLayering\Providers
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
        $config_path = __DIR__ . '/../../../config/repositories.php';  // Config path.

        $this->publishes([$config_path => config_path('repositories.php')], 'repositories'); // Publish config.
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
        $config_path = __DIR__ . '/../../../config/repositories.php';
        // Merge config.
        $this->mergeConfigFrom(
            $config_path,
            'repositories'
        );
    }
    /**
     * Register the bindings.
     */
    protected function registerBindings()
    {
        $this->app->instance('FileSystem', new Filesystem()); // FileSystem.
        
        $this->app->bind('Composer', function ($app) { // Composer.
            return new Composer($app['FileSystem']);
        });
    
        $this->app->singleton('RepositoryCreator', function ($app) { // Repository creator.
            return new RepositoryCreator($app['FileSystem']);
        });
        
        $this->app->singleton('CriteriaCreator', function ($app) { // Criteria creator.
            return new CriteriaCreator($app['FileSystem']);
        });
    }

    /**
     * Register the make:repository command.
     */
    protected function registerMakeRepositoryCommand()
    {
        // Make repository command.
        $this->app['command.repository.make'] = $this->app->share(function($app) {
            return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
        });
    }

    /**
     * Register the make:criteria command.
     */
    protected function registerMakeCriteriaCommand()
    {
        // Make criteria command.
        $this->app['command.criteria.make'] = $this->app->share(function($app){
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