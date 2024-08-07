<?php

namespace Ferdiunal\NovaTranslations\Translators;

use Error;
use Exception;
use RuntimeException;

class DeepLTranslator extends Translator
{
    public function handle(string $source, string $target, string $text): string
    {
        if (class_exists("\DeepL\Translator") === false) {
            throw new RuntimeException(
                'The package deeplcom/deepl-php is not installed. Please run `composer require deeplcom/deepl-php`',
            );
        }

        $authKey = config('nova-translations.services.deepl.api_key');

        if ($authKey === null) {
            throw new RuntimeException(
                'The DeepL API key is not set. Please set the key in the environment variable DEEPL_API_KEY=xxxxxxx-...',
            );
        }

        try {
            $translator = new \DeepL\Translator($authKey);
            $translate = $translator->translateText(
                $text,
                $source,
                $target
            );

            return $translate->text;
        } catch (Exception|Error|RuntimeException $e) {
            return $text;
        }
    }

    public function icon(): string
    {
        return 'https://cdn.worldvectorlogo.com/logos/deepl-1.svg';
    }

    public function key(): string
    {
        return 'deepl';
    }

    public function title(): string
    {
        return 'DeepL';
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
