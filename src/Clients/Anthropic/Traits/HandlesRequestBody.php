<?php

namespace Cognesy\Instructor\Clients\Anthropic\Traits;

use Cognesy\Instructor\ApiClient\Enums\ClientType;
use Cognesy\Instructor\Enums\Mode;

trait HandlesRequestBody {
    public function messages(): array {
        if ($this->noScript()) {
            return $this->messages;
        }

        if ($this->script->section('examples')->notEmpty()) {
            $this->script->section('pre-examples')->appendMessage([
                'role' => 'assistant',
                'content' => 'Provide examples.',
            ]);
        }

        $this->script->section('pre-input')->appendMessage([
            'role' => 'user',
            'content' => "Analyze following context and respond to user prompt.",
        ]);

        if($this->mode->is(Mode::Tools)) {
            unset($this->scriptContext['json_schema']);
        }

        return $this->script
            ->withContext($this->scriptContext)
            ->select(['pre-input', 'messages', 'input', 'data-ack', 'prompt', 'pre-examples', 'examples', 'retries'])
            ->toSingleSection('merged')
            ->toAlternatingRoles()
            ->toNativeArray(ClientType::fromRequestClass(static::class));
    }

    public function system(): string {
        if ($this->noScript()) {
            return $this->system;
        }

        return $this->script
            ->withContext($this->scriptContext)
            ->select(['system'])
            ->toString();
    }

    public function getToolChoice(): string|array {
        return match(true) {
            empty($this->tools) => '',
            is_array($this->toolChoice) => [
                'type' => 'tool',
                'name' => $this->toolChoice['function']['name'],
            ],
            empty($this->toolChoice) => [
                'type' => 'auto',
            ],
            default => [
                'type' => $this->toolChoice,
            ],
        };
    }

    public function tools(): array {
        if (empty($this->tools)) {
            return [];
        }

        $anthropicFormat = [];
        foreach ($this->tools as $tool) {
            $anthropicFormat[] = [
                'name' => $tool['function']['name'],
                'description' => $tool['function']['description'] ?? '',
                'input_schema' => $tool['function']['parameters'],
            ];
        }

        return $anthropicFormat;
    }

    protected function getResponseFormat(): array {
        return [];
    }

    protected function getResponseSchema() : array {
        return $this->responseFormat['schema'] ?? [];
    }
}
