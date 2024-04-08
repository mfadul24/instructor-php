<?php
namespace Cognesy\Instructor\ApiClient\Data\Requests;

use Cognesy\Instructor\Utils\Json;

class ApiJsonCompletionRequest extends ApiRequest
{
    protected string $prompt = "\nRespond correctly with JSON object. Response must follow provided JSONSchema.\n";

    public function __construct(
        public string|array $messages = [],
        public array $responseFormat = [],
        public string $model = '',
        public array $options = [],
    ) {
        if (!is_array($messages)) {
            $this->messages = ['role' => 'user', 'content' => $messages];
        }
        parent::__construct([], $this->getEndpoint());
    }

    protected function defaultBody(): array {
        return array_filter(array_merge($this->payload, [
            'messages' => $this->getMessages(),
            'model' => $this->model,
            'response_format' => $this->getResponseFormat(),
        ], $this->options));
    }

    protected function getMessages(): array {
        return $this->appendInstructions($this->messages, $this->getResponseSchema());
    }

    protected function getResponseFormat(): array {
        return $this->responseFormat;
    }

    private function getResponseSchema() : array {
        return $this->responseFormat['schema'] ?? [];
    }

    protected function appendInstructions(array $messages, array $jsonSchema) : array {
        $lastIndex = count($messages) - 1;
        if (!isset($messages[$lastIndex]['content'])) {
            $messages[$lastIndex]['content'] = '';
        }
        $messages[$lastIndex]['content'] .= $this->prompt . ($jsonSchema ? "Expected JSONSchema of response:\n".Json::encode($jsonSchema) : '');
        return $messages;
    }
}