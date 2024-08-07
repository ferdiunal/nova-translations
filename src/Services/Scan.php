<?php

/**
 * This class is derived from TomatoPHP's Filament Translations package.
 *
 * @author TomatoPHP <tomatophp.com@gmail.com>
 *
 * @link https://github.com/tomatophp/filament-translations/blob/master/src/Services/Scan.php
 */

namespace Ferdiunal\NovaTranslations\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class Scan
{
    /**
     * @return array<\Illuminate\Support\Collection, \Illuminate\Support\Collection, \Illuminate\Support\Collection>
     */
    public function __invoke(): array
    {
        $patternA = config('nova-translations.patternA');

        $patternB = config('nova-translations.patternB');

        $patternC = config('nova-translations.patternC');

        if (! $patternA) {
            throw new \Exception('Pattern A is not set in the configuration file.');
        } elseif (! $patternB) {
            throw new \Exception('Pattern B is not set in the configuration file.');
        } elseif (! $patternC) {
            throw new \Exception('Pattern C is not set in the configuration file.');
        }

        $trans = collect();
        $__ = collect();
        $langs = [];
        $excludedPaths = config('nova-translations.excludedPaths');

        // FIXME maybe we can count how many times one translation is used and eventually display it to the user

        /** @var SplFileInfo $file */
        foreach (File::allFiles(config('nova-translations.paths')) as $file) {
            $dir = dirname($file);
            if (Str::startsWith($dir, $excludedPaths)) {
                continue;
            }

            if (
                str($file->getPathname())->contains('/lang/') &&
                (
                    str($file->getPathname())->contains(sprintf('/%s/', config('app.locale'))) &&
                    $file->getExtension() === 'php'
                )
                || str($file->getPathname())->contains(sprintf('%s.json', config('app.locale')))
            ) {
                if ($file->getExtension() === 'php') {
                    $langs = [
                        ...$langs,
                        ...require $file->getPathname(),
                    ];
                } else {
                    $langs = [
                        ...$langs,
                        ...((array) json_decode($file->getContents(), true)),
                    ];
                }

                continue;
            }

            if (preg_match_all("/$patternA/siU", $file->getContents(), $matches)) {
                $trans->push($matches[2]);
            }

            if (preg_match_all("/$patternB/siU", $file->getContents(), $matches)) {
                $__->push($matches[2]);
            }

            if (preg_match_all("/$patternC/siU", $file->getContents(), $matches)) {
                $__->push($matches[2]);
            }
        }

        $langs = collect(Arr::dot($langs))->filter(
            fn ($_) => ! in_array($_, [
                ',', '&mdash;', ':-(', '*', '—',
            ], true) && ! is_array($_)
        );
        $result = [
            $trans->flatten()
                ->unique()
                ->filter(
                    fn ($_) => ! in_array($_, [
                        ',', '&mdash;', ':-(', '*', '—',
                    ], true)
                ),
            $__->flatten()
                ->merge(
                    $langs
                        ->values()
                        ->toArray()
                )
                ->unique()
                ->filter(
                    fn ($_) => ! in_array($_, [
                        ',', '&mdash;', ':-(', '*', '—',
                    ], true)
                ),
            $langs,
        ];

        return $result;
    }
}
