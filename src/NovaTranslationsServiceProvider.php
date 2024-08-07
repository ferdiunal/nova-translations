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
        $resource = $this->app['config']->get('nova-translations.resource', TranslationResource::class);

        if (!$this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/nova-translations.php', 'nova-translations');
        }

        if ($this->app->runningInConsole()) {
            //Register Migrations
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/nova-translations.php' => config_path('nova-translations.php'),
        ], 'nova-translations-config');

        //Publish Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'nova-translations-migrations');

        $this->app->booted(function () use (&$resource) {
            $config = $this->app['config'];
            $config->set(
                'translation-loader.model',
                $config->get('nova-translations.model')
            );

            $resource::$model = $config->get('nova-translations.model');

            $this->commands([
                ImportScanCommand::class,
            ]);

            $this->routes();
        });

        Nova::serving(function (ServingNova $event) use (&$resource) {
            Nova::resources([
                $resource,
            ]);

            Nova::$translations = Translation::getTranslationsForGroup(
                $this->app->getLocale(),
                '*'
            );
        });
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
            ->group(__DIR__ . '/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/ferdiunal/nova-translations')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
