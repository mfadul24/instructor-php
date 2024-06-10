<?php
namespace Cognesy\Instructor\Data\Traits\Request;

trait HandlesApiRequestOptions
{
    private array $options = [];

    public function options() : array {
        return $this->options;
    }

    public function option(string $key, mixed $defaultValue = null) : mixed {
        return $this->options[$key] ?? $defaultValue;
    }

    public function setOption(string $name, mixed $value) : self {
        $this->options[$name] = $value;
        return $this;
    }

    public function unsetOption(string $name) : self {
        unset($this->options[$name]);
        return $this;
    }
}