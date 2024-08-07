<?php

namespace Ferdiunal\NovaTranslations\Translators;

use Error;
use Exception;
use RuntimeException;

class NLPCloudTranslator extends Translator
{
    public function handle(string $source, string $target, string $text): string
    {
        if (class_exists("\NLPCloud\NLPCloud") === false) {
            throw new RuntimeException(
                'The package nlpcloud/nlpcloud-client is not installed. Please run `composer require nlpcloud/nlpcloud-client`',
            );
        }

        $authKey = config('nova-translations.services.nlpcloud.api_key');
        $sourceLang = config("nova-translations.services.nlpcloud.languages.{$source}");
        $targetLang = config("nova-translations.services.nlpcloud.languages.{$target}");

        if ($authKey === null) {
            throw new RuntimeException(
                'The NLPCLoud API key is not set. Please set the key in the environment variable NLPCLOUD_API_KEY=xxxxxxx...',
            );
        }

        try {
            $translator = new \NLPCloud\NLPCloud('nllb-200-3-3b', $authKey, false);
            $translate = $translator->translation(
                $text,
                $sourceLang,
                $targetLang
            );

            return data_get($translate, 'translation_text');
        } catch (Exception|Error|RuntimeException $e) {
            return $text;
        }
    }

    public function icon(): string
    {
        return 'https://nlpcloud.com/assets/images/logo.svg';
    }

    public function key(): string
    {
        return 'nlpcloud';
    }

    public function title(): string
    {
        return 'NLP Cloud';
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
