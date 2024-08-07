<?php

namespace Ferdiunal\NovaTranslations\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class BingTranslatorTool
{
    private string $apiAuth = 'https://edge.microsoft.com/translate/auth';

    private string $apiTranslate = 'https://api.cognitive.microsofttranslator.com/translate';

    private readonly Client $client;

    public function __construct(
        private readonly string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299'
    ) {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $this->userAgent,
            ],
        ]);
    }

    /**
     * @return array<string, string>
     *
     * @throws RequestException
     * @throws GuzzleException
     */
    private function getToken()
    {
        try {
            $response = $this->client->get($this->apiAuth);
            $authJWT = (string) $response->getBody();
            $jwtPayload = json_decode(base64_decode(explode('.', $authJWT)[1]), true);

            return [
                'token' => $authJWT,
                'tokenExpiresAt' => $jwtPayload['exp'] * 1000,
            ];
        } catch (RequestException $e) {
            throw $e;
        }
    }

    public function translate(string $text, ?string $source = null, string $target = 'en')
    {
        $config = $this->getToken();

        try {
            $response = $this->client->post($this->apiTranslate, [
                'query' => [
                    'api-version' => '3.0',
                    'from' => $source,
                    'to' => $target,
                ],
                'json' => [['Text' => $text]],
                'headers' => [
                    'User-Agent' => $this->userAgent,
                    'Authorization' => $config['token'],
                ],
            ]);

            return data_get(
                json_decode($response->getBody(), true) ?? [],
                '0.translations.0.text',
                $text
            );
        } catch (RequestException $e) {
            // We don't need an error message here, it is enough to return the original text if an error occurred.
            return $text;
        }
    }
}
