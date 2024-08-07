<?php

namespace Ferdiunal\NovaTranslations;

use Ferdiunal\NovaTranslations\Translators\Translater;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Laravel\Nova\Fields\Text;

class TranslationField extends Text
{
    /** @var array<int, Translater> */
    public array $translaters = [];

    public function __construct(
        string $name,
        ?string $attribute = null,
        ?callable $resolveCallback = null
    ) {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'locales' => collect(config('nova-translations.locals'))
                ->sortBy(function ($_, $locale) {
                    return $locale === config('app.locale') ? 0 : 1;
                })->toArray(),
        ]);

        collect(config('nova-translations.translaters', []))
            ->each(fn (string $translater) => $this->setTranslater(new $translater));
    }

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-translations';

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);
        if ($this->value === null) {
            $this->value = collect(config('nova-translations.locals', []))
                ->mapWithKeys(fn ($_, $locale) => [$locale => ''])
                ->toArray();
        }
    }

    public function setTranslater(
        Translator $translater
    ): self {
        $this->translaters[] = $translater;
        $this->withMeta([
            'translaters' => array_map(
                fn (Translator $translater) => $translater->toArray(),
                $this->translaters
            ),
        ]);

        return $this;
    }
}
