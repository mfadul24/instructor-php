<?php

namespace Cognesy\Instructor\Extras\Web\Scrapers;

use Cognesy\Instructor\Extras\Web\Contracts\CanGetUrlContent;
use Cognesy\Instructor\Utils\Env;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class ScrapFlyDriver implements CanGetUrlContent {
    private string $baseUrl;
    private string $apiKey;
    private ClientInterface $client;

    public function __construct(string $baseUrl = '', string $apiKey = '') {
        $this->baseUrl = $baseUrl ?: Env::get('SCRAPFLY_BASE_URI');
        $this->apiKey = $apiKey ?: Env::get('SCRAPFLY_API_KEY');
        $this->client = new Client();
    }

    public static function fromUrl(string $url, array $options = []) : string {
        return (new self(
            baseUrl: $options['base_url'] ?? '',
            apiKey: $options['api_key'] ?? ''
        ))->getContent($url, $options);
    }

    public function getContent(string $url, array $options = []): string {
        $apiUrl = $this->makeUrl($url);
        $request = new Request('GET', $apiUrl);

        try {
            $response = $this->client->sendRequest($request);
            $content = $response->getBody()->getContents();
            $json = json_decode($content, true);
            return $json['result']['content'];
        } catch (Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }

    // INTERNAL ///////////////////////////////////////////

    private function makeUrl(string $url) : string {
        $fields = [
            'key' => $this->apiKey,
            'url' => $url,
            'render_js' => 'false',
            'asp' => 'false',
            'format' => 'raw',
        ];
        return $this->baseUrl . '?' . http_build_query($fields);
    }
}
