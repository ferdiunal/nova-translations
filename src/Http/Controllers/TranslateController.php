<?php

namespace Ferdiunal\NovaTranslations\Http\Controllers;

use App\Http\Controllers\Controller;
use Arr;
use Ferdiunal\NovaTranslations\TranslationField;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Laravel\Nova\Http\Requests\NovaRequest;

class TranslateController extends Controller
{
    public function __invoke(
        string $resourceName,
        string $resourceId,
        NovaRequest $request
    ) {
        $validated = $request->validate([
            'attribute' => ['required', 'string', 'max:50'],
            'source' => ['required', 'string', 'size:2'],
            'target' => ['required', 'string', 'size:2'],
            'translater' => ['required', 'string', 'max:50'],
            'currentValue' => ['nullable', 'string', 'max:255'],
        ]);

        if ($resourceId === 'creation') {
            $resource = $request->newResource();
        } else {
            $resource = $request->findResourceOrFail(
                $resourceId
            );
        }
        $resource->authorizeToView($request);

        abort_unless($resource::trafficCop($request), 403);

        if ($resourceId === 'creation') {
            /** @var TranslationField */
            $field = $resource->creationFields($request)
                ->findFieldByAttribute($validated['attribute'], function () {
                    abort(404);
                });
        } else {
            /** @var TranslationField */
            $field = $resource->updateFields($request)
                ->findFieldByAttribute($validated['attribute'], function () {
                    abort(404);
                });
        }

        $translater = Arr::first(
            $field->translaters,
            fn (Translator $translater) => $translater->key() === $validated['translater'],
            []
        );

        abort_if(empty($translater), 404);
        $fieldValue = $field->value[$validated['source']] ?? $field->value[$validated['source']] ?? $field->value[config('app.fallback_locale')];

        if (empty($fieldValue)) {
            $fieldValue = $validated['currentValue'];
        }

        $text = $translater->run(
            $validated['source'],
            $validated['target'],
            $fieldValue
        );

        return response(compact('text'));
    }
}
