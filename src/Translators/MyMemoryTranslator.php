<?php

namespace Ferdiunal\NovaTranslations\Translators;

use Illuminate\Support\Facades\Http;

class MyMemoryTranslator extends Translator
{
    public function handle(string $source, string $target, string $text): string
    {
        $apiUrl = 'https://api.mymemory.translated.net/get';

        $query = [
            'q' => $text,
            'langpair' => sprintf('%s|%s', $source, $target),
            'mt' => '1',
        ];

        $response = Http::get($apiUrl, $query);

        return data_get($response->json(), 'responseData.translatedText', $text);
    }

    public function icon(): string
    {
        return 'https://mymemory.translated.net/public/img/mym_logo_horizontal.svg';
    }

    public function key(): string
    {
        return 'mymemory';
    }

    public function title(): string
    {
        return 'MyMemory';
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
