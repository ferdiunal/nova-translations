<?php

namespace Ferdiunal\NovaTranslations\Commands;

use Arr;
use Ferdiunal\NovaTranslations\Services\SaveScan;
use Ferdiunal\NovaTranslations\Translators\Translator;
use Illuminate\Console\Command;

use function Laravel\Prompts\spin;

class ImportScanCommand extends Command
{
    /** @var array<int, Translater> */
    protected array $translaters = [];

    protected $signature = 'nova-translations:import {--T|translater= : The translater to use: %s}';

    protected $description = 'Import translations from the language files';

    public function __construct(...$args)
    {
        $this->translaters = array_map(
            fn ($translater) => app($translater),
            config('nova-translations.translaters', [])
        );

        $this->signature = sprintf(
            'nova-translation:import {--T|translater= : The translater to use: %s}',
            implode(',', array_map(
                fn (Translator $translater) => $translater->key(),
                $this->translaters
            ))
        );

        parent::__construct(...$args);
    }

    public function handle()
    {
        $start = microtime(true);

        $translater = Arr::first(
            $this->translaters,
            fn (Translator $translater) => $translater->key() === $this->option('translater'),
        );

        $scanner = new SaveScan;

        if ($translater instanceof Translator) {
            $scanner->setTranslator($translater);
        }

        spin(
            function () use (&$scanner) {
                $scanner->handle();
            },
            'Importing translations...',
        );

        $this->info(
            sprintf(
                'Translations imported in %s seconds',
                number_format(microtime(true) - $start, 2)
            )
        );
    }
}
