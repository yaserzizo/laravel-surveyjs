<?php

namespace Fruitware\LaravelSurveyJS;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelSurveyJsServiceProvider
 *
 * @package Fruitware/LaravelSurveyJS
 */
class LaravelSurveyJsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'aidynmakhataev');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'survey-manager');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'../database/migrations');
      //  $this->loadRoutesFrom(base_path('routes/vendor/survey-manager/web.php'));
      //  $this->loadRoutesFrom(base_path('routes/vendor/survey-manager/api.php'));


        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            // Publishing the configuration file.

            $this->definePublishable();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../config/survey-manager.php', 'survey-manager'
        );



    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['survey-manager'];
    }

    private function definePublishable()
    {
/*        $this->publishes([
            __DIR__.'/../config/survey-manager.php' => config_path('survey-manager.php'),
        ]);

        $this->publishes([
            realpath(__DIR__.'/../database/migrations') => database_path('migrations'),
        ], 'migrations');*/

        $this->publishes([
           realpath(__DIR__.'/../public') => public_path('vendor/survey-manager'),
        ], 'public');

/*        $this->publishes([
           realpath(__DIR__.'/../resources/views') => resource_path('views/vendor/survey-manager'),
        ]);

        $this->publishes([
            realpath(__DIR__.'/routes') => base_path('routes/vendor/survey-manager'),
        ]);*/
       // $this->loadRoutesFrom(base_path('routes/vendor/survey-manager'));

    }
}
