<?php

namespace Ferdiunal\NovaTranslations\Translators;

abstract class Translator
{
    /**
     * Get the icon of the translater
     */
    abstract public function icon(): string;

    /**
     * Get the title of the translater
     */
    abstract public function title(): string;

    /**
     * Get the key of the translater
     */
    abstract public function key(): string;

    /**
     * Get the translater as an array
     */
    abstract public function toArray(): array;

    /**
     * Translate the text
     */
    abstract public function handle(string $source, string $target, string $text): string;

    public function run(string $source, string $target, string $text): string
    {
        $_matches = [];
        $formattedValue = preg_replace_callback("/:\w+/", function ($matches) use (&$_matches) {
            $_matches = [...$_matches, ...$matches];

            return str_repeat('@', count($_matches));
        }, $text);

        $value = $this->handle($source, $target, $formattedValue);

        foreach ($_matches as $key => $match) {
            $value = str_replace(str_repeat('@', $key + 1), $match, $value);
        }

        return $value;
    }
}
