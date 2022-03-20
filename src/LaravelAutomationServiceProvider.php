<?php

namespace Scheel\Automation;

use Illuminate\Support\ServiceProvider;
use Scheel\Automation\Commands\RunAutomationsCommand;

class LaravelAutomationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mortenscheel');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mortenscheel');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/automation.php', 'automation');

        // Register the service the package provides.
        $this->app->singleton('laravel-automation', function ($app) {
            return new LaravelAutomation();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-automation'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/automation.php' => config_path('automation.php'),
        ], 'automation-config');
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'automation-migrations');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/mortenscheel'),
        ], 'laravel-automation.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/mortenscheel'),
        ], 'laravel-automation.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/mortenscheel'),
        ], 'laravel-automation.views');*/

        // Registering package commands.
        $this->commands([
            RunAutomationsCommand::class,
        ]);
    }
}
