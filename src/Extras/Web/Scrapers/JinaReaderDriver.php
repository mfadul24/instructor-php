<?php

namespace Cognesy\Instructor\Extras\Web\Scrapers;

use Cognesy\Instructor\Extras\Web\Contracts\CanGetUrlContent;
use Cognesy\Instructor\Utils\Env;

class JinaReaderDriver implements CanGetUrlContent {
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $baseUrl = '', string $apiKey = '') {
        $this->baseUrl = $baseUrl ?: Env::get('JINA_READER_BASE_URI', '');
        $this->apiKey = $apiKey ?: Env::get('JINA_READER_API_KEY', '');
    }

    public static function fromUrl(string $url, array $options = []) : string {
        return (new self(
            baseUrl: $options['base_url'] ?? '',
            apiKey: $options['api_key'] ?? ''
        ))->getContent($url, $options);
    }

    public function getContent(string $url, array $options = []) : string {
        $url = $this->baseUrl . $url . '&api_key=' . $this->apiKey;
        return file_get_contents($url);
    }
}

//curl https://r.jina.ai/https://example.com \
//	-H "X-Return-Format: html"
