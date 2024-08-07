<?php

namespace Ferdiunal\NovaTranslations\Translators;

class GoogleTranslator extends Translator
{
    public function handle(string $source, string $target, string $text): string
    {
        if (! class_exists(\Stichoza\GoogleTranslate\GoogleTranslate::class)) {
            throw new \Exception('Google Translate package not found. Please install it by running "composer require stichoza/google-translate-php"');
        }

        $translater = new \Stichoza\GoogleTranslate\GoogleTranslate(
            source: $source,
            target: $target
        );

        return $translater->translate($text);
    }

    public function icon(): string
    {
        return 'https://upload.wikimedia.org/wikipedia/commons/d/db/Google_Translate_Icon.png';
    }

    public function key(): string
    {
        return 'google';
    }

    public function title(): string
    {
        return 'Google';
    }

    public function toArray(): array
    {
        return [
            'icon' => $this->icon(),
            'key' => $this->key(),
            'title' => $this->title(),
        ];
    }
}
