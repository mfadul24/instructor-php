<?php

namespace Cognesy\Instructor\Clients\OpenAI\Traits;

use Cognesy\Instructor\ApiClient\Enums\ClientType;
use Cognesy\Instructor\Enums\Mode;

trait HandlesRequestBody
{
    public function messages(): array {
        if ($this->noScript()) {
            return $this->messages;
        }

        $this->script->section('pre-input')->appendMessage([
            'role' => 'user',
            'content' => "Analyze following context and respond to user prompt.",
        ]);

//        $this->script->section('pre-input')->appendMessage([
//            'role' => 'assistant',
//            'content' => "Provide input.",
//        ]);

        if ($this->script->section('examples')->notEmpty()) {
            $this->script->section('pre-examples')->appendMessage([
                'role' => 'assistant',
                'content' => 'Provide examples.',
            ]);
        }
        if($this->mode->is(Mode::Tools)) {
            unset($this->scriptContext['json_schema']);
        }

        return $this->script
            ->withContext($this->scriptContext)
            ->select(['system', 'pre-input', 'messages', 'input', 'prompt', 'pre-examples', 'examples', 'retries'])
            ->toAlternatingRoles()
            ->toNativeArray(ClientType::OpenAI, $this->scriptContext);
    }

    public function tools(): array {
        return $this->tools;
    }

    public function getToolChoice(): string|array {
        if (empty($this->tools)) {
            return '';
        }
        return $this->toolChoice ?: 'auto';
    }

    protected function getResponseSchema() : array {
        return $this->responseFormat['schema'] ?? [];
    }

    protected function getResponseFormat(): array {
        return $this->responseFormat['format'] ?? [];
    }
}