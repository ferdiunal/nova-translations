<?php

namespace Ferdiunal\NovaTranslations\Services;

use Ferdiunal\NovaTranslations\Models\Translation;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

final class SaveScan
{
    protected ?Translator $translator = null;

    protected bool $autoTranslate;

    protected string $defaultLocale;

    protected \Illuminate\Support\Collection $_langs;

    public function __construct()
    {
        $this->defaultLocale = config('app.locale');
        $this->_langs = collect([]);
    }

    public function setTranslator(Translator $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function handle()
    {
        $scanner = new Scan;
        /**
         * @var \Illuminate\Support\Collection $trans
         * @var \Illuminate\Support\Collection $__
         * @var \Illuminate\Support\Collection $_langs
         */
        [$trans, $__, $_langs] = $scanner();

        $this->_langs = $_langs;
        DB::transaction(function () use ($trans, $__) {
            $trans->each(fn ($trans) => $this->processTranslation($trans));
            $__->each(fn ($default) => $this->save('*', '*', $default, $default));
        });
    }

    protected function processTranslation(string $trans): void
    {
        [$group, $key] = explode('.', $trans, 2);
        $namespaceAndGroup = explode('::', $group, 2);
        $namespace = count($namespaceAndGroup) === 1 ? '*' : $namespaceAndGroup[0];
        $group = count($namespaceAndGroup) === 1 ? $namespaceAndGroup[0] : $namespaceAndGroup[1];
        $this->save($namespace, $group, $key, $trans);
    }

    protected function save(string $namespace, string $group, string $key, ?string $mainKey = null): void
    {
        $locals = array_keys(config('nova-translations.locals'));
        $trans = $this->lang($mainKey, [], $this->defaultLocale);
        $text = [
            $this->defaultLocale => $trans,
        ];

        $model = app(config('nova-translations.model'));

        if (! is_a($model, Translation::class, true)) {
            throw new \Exception('Model must be an instance of '.Translation::class);
        }

        foreach ($locals as $locale) {
            $text[$locale] = $this->lang($mainKey, [], $locale);
        }

        $model::query()->upsert(
            [
                'namespace' => $namespace,
                'group' => $group,
                'key' => $key,
                'text' => json_encode($text, JSON_UNESCAPED_UNICODE),
            ],
            ['namespace', 'group', 'key'],
            ['text']
        );
    }

    protected function lang($key, $replace = null, $locale = null): string
    {
        $trans = $this->_langs->get(
            $key,
            Lang::get(
                $key,
                $replace,
                $this->defaultLocale
            )
        );

        if (is_array($trans)) {
            return $key;
        }

        $isTranslatable = (str($trans)->contains('::') || str($trans)->contains('_') ||
            (! str($trans)->contains('::') &&
                ! str($trans)->endsWith('.') &&
                str($trans)->contains('.')));

        if ($this->defaultLocale !== $locale) {
            if ($this->translater instanceof Translator && ! $isTranslatable) {
                $trans = $this->translater->run(
                    source: $this->defaultLocale,
                    target: $locale,
                    text: $trans
                );
            }
        }

        ray(compact('trans', 'locale'));

        return $trans;
    }
}
