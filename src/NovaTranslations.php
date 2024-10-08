<?php

namespace Ferdiunal\NovaTranslations;

use Ferdiunal\NovaTranslations\Nova\TranslationResource;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaTranslations extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-translation-tool', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-translation-tool', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make(__('Translation Tool'))
            ->path('/resources/'.TranslationResource::uriKey())
            ->canSee(function ($request) {
                return TranslationResource::authorizedToViewAny($request);
            })
            ->icon('translate');
    }
}
