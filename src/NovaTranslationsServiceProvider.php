<?php

namespace Ferdiunal\NovaTranslations;

use Ferdiunal\NovaTranslations\Commands\ImportScanCommand;
use Ferdiunal\NovaTranslations\Http\Middleware\Authorize;
use Ferdiunal\NovaTranslations\Models\Translation;
use Ferdiunal\NovaTranslations\Nova\TranslationResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;

class NovaTranslationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerResources();
        $this->registerMigrations();
        $this->registerCommands();

        Nova::serving(function (ServingNova $event) {
            $this->registerNovaResources();
            $this->loadNovaTranslations();
        });
    }

    /**
     * Register package configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/nova-translations.php', 'nova-translations');
        }
    }

    /**
     * Register Nova resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $resource = $this->getConfiguredResource();
        $this->app->booted(function () use ($resource) {
            $this->setTranslationModel($resource);
            $this->routes();
        });
    }

    /**
     * Register migrations for the package.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/nova-translations.php' => config_path('nova-translations.php'),
            ], 'nova-translations-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'nova-translations-migrations');
        }
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportScanCommand::class,
            ]);
        }
    }

    /**
     * Register Nova-specific resources.
     *
     * @return void
     */
    protected function registerNovaResources()
    {
        $resource = $this->getConfiguredResource();
        Nova::resources([$resource]);
    }

    /**
     * Load translations for Nova interface.
     *
     * @return void
     */
    protected function loadNovaTranslations()
    {
        Nova::$translations = Translation::getTranslationsForGroup(
            $this->app->getLocale(),
            '*'
        );
    }

    /**
     * Set the translation model for the resource.
     *
     * @param  string  $resource
     * @return void
     */
    protected function setTranslationModel($resource)
    {
        $config = $this->app['config'];
        $config->set('translation-loader.model', $config->get('nova-translations.model'));
        $resource::$model = $config->get('nova-translations.model');
    }

    /**
     * Get the configured Nova resource class.
     *
     * @return string
     */
    protected function getConfiguredResource()
    {
        return $this->app['config']->get('nova-translations.resource', TranslationResource::class);
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'nova-translations')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/ferdiunal/nova-translations')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register any application services or bindings here
    }
}
