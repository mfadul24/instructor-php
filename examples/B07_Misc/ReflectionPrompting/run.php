---
title: 'Reflection Prompting'
docname: 'reflection_prompting'
---

## Overview

This implementation of Reflection Prompting with Instructor provides a structured way
to encourage LLM to engage in more thorough and self-critical thinking processes,
potentially leading to higher quality and more reliable outputs.


## Example

```php
<?php
$loader = require 'vendor/autoload.php';
$loader->add('Cognesy\\Instructor\\', __DIR__.'../../src/');

use Cognesy\Instructor\Enums\Mode;
use Cognesy\Instructor\Instructor;
use Cognesy\Instructor\Schema\Attributes\Instructions;
use Cognesy\Instructor\Validation\Contracts\CanValidateSelf;
use Cognesy\Instructor\Validation\ValidationResult;

class ReflectiveResponse implements CanValidateSelf {
    #[Instructions('Is problem solvable and what domain expertise it requires')]
    public string $assessment;
    #[Instructions('Describe an expert persona who would be able to solve this problem, their skills and experience')]
    public string $persona;
    #[Instructions("Initial analysis and expert persona's approach to the problem")]
    public string $initialThinking;
    #[Instructions('Steps of reasoning leading to the final answer - expert persona thinking through the problem')]
    /** @var string[] */
    public array $chainOfThought;
    #[Instructions('Critical examination of the reasoning process - what could go wrong, what are the assumptions')]
    public string $reflection;
    #[Instructions('Final answer after reflection')]
    public string $finalOutput;

    // Validation method to ensure thorough reflection
    public function validate(): ValidationResult {
        $errors = [];
        if (empty($this->reflection)) {
            $errors[] = "Reflection is required for a thorough response.";
        }
        if (count($this->chainOfThought) < 2) {
            $errors[] = "Please provide at least two steps in the chain of thought.";
        }
        return ValidationResult::make($errors);
    }
}

$problem = 'Solve the equation x+y=x-y';
$solution = (new Instructor)->withClient('anthropic')->respond(
    messages: $problem,
    responseModel: ReflectiveResponse::class,
    mode: Mode::MdJson,
    options: ['max_tokens' => 2048]
);

print("Problem:\n$problem\n\n");
dump($solution);

?>
```