<?php

namespace Ferdiunal\NovaTranslations\Http\Controllers;

use App\Http\Controllers\Controller;
use Arr;
use Ferdiunal\NovaTranslations\TranslationField;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Laravel\Nova\Http\Requests\NovaRequest;

class AllTranslateController extends Controller
{
    public function __invoke(
        string $resourceName,
        string $resourceId,
        NovaRequest $request
    ) {
        $validated = $request->validate([
            'attribute' => ['required', 'string', 'max:50'],
            'source' => ['required', 'string', 'size:2'],
            'targets' => ['required', 'array'],
            'targets.*' => ['required', 'string', 'size:2'],
            'translater' => ['required', 'string', 'max:50'],
        ]);

        $resource = $request->findResourceOrFail($resourceId);
        $resource->authorizeToView($request);

        if ($resource::trafficCop($request) === false) {
            return false;
        }

        /** @var TranslationField */
        $field = $resource->updateFields($request)
            ->findFieldByAttribute($validated['attribute'], function () {
                abort(404);
            });

        $translater = Arr::first(
            $field->translaters,
            fn (Translator $translater) => $translater->key() === $validated['translater'],
            []
        );

        abort_if(empty($translater), 404);
        $text = [];
        foreach ($validated['targets'] as $target) {
            $text[$target] = $translater->run(
                $validated['source'],
                $target,
                $field->value[$validated['source']] ?? $field->value[$validated['source']] ?? $field->value[config('app.fallback_locale')]
            );
        }

        return response(compact('text'));
    }
}
