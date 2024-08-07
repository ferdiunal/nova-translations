<?php

namespace Ferdiunal\NovaTranslations\Nova;

use Ferdiunal\NovaTranslations\Models\Translation;
use Ferdiunal\NovaTranslations\TranslationField;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class TranslationResource extends Resource
{
    /**
     * @var class-string<\Ferdiunal\NovaTranslations\Models\Translation>
     */
    public static $model = Translation::class;

    public static $title = 'namespace';

    public static $displayInNavigation = false;

    public static $search = [
        'namespace', 'group', 'key', 'text', 'meta',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make('ID', 'id')->sortable(),
            Text::make('Namespace')->sortable(),
            Text::make('Group')->sortable(),
            Text::make('Key')->sortable(),
            TranslationField::make('Text'),
            Text::make('Meta')->sortable()->hideFromIndex(),
        ];
    }

    /**
     * Apply the default orderings for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation
     */
    public static function defaultOrderings($query)
    {
        $table = app(static::$model)->getTable();

        return $query->reorder()->orderBy($table . '.namespace', 'asc')->orderBy($table . '.group', 'asc')->orderBy($table . '.key', 'asc');
    }
}
