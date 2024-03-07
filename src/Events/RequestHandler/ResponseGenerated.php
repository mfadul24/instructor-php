<?php

namespace Cognesy\Instructor\Events\RequestHandler;

use Cognesy\Instructor\Events\Event;

class ResponseGenerated extends Event
{

    /**
     * @param mixed $response
     */
    public function __construct(
        public mixed $response
    )
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        return $this->format(json_encode($this->response));
    }
}