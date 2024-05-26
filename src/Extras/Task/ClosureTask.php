<?php

namespace Cognesy\Instructor\Extras\Task;

use Closure;
use Cognesy\Instructor\Extras\Signature\SignatureFactory;

class ClosureTask extends ExecutableTask
{
    private Closure $callable;

    public function __construct(Closure $callable) {
        $signature = SignatureFactory::fromCallable($callable);
        parent::__construct($signature);
        $this->callable = $callable;
    }

    public function forward(mixed ...$args): mixed {
        return ($this->callable)(...$args);
    }
}