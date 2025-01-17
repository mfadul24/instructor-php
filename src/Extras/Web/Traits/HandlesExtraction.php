<?php

namespace Cognesy\Instructor\Extras\Web\Traits;

use Cognesy\Instructor\Extras\Web\Webpage;
use Generator;

trait HandlesExtraction
{
    public function get(string $url, array $options = []) : static {
        $this->url = $url;
        $this->content = $this->scraper->getContent($url, $options);
        if ($options['cleanup'] ?? false) {
            $this->content = $this->htmlProcessor->cleanup($this->content);
        }
        return $this;
    }

    public function cleanup() : static {
        $this->content = $this->htmlProcessor->cleanup($this->content);
        return $this;
    }

    public function select(string $selector) : static {
        $this->content = $this->htmlProcessor->select($this->content, $selector);
        return $this;
    }

    /**
     * @param string $selector CSS selector
     * @param callable|null $fn Function to transform the selected item
     * @return Generator<Webpage> a generator of Webpage objects
     */
    public function selectMany(string $selector, callable $fn = null) : Generator {
        foreach ($this->htmlProcessor->selectMany($this->content, $selector) as $html) {
            yield match($fn) {
                null => Webpage::withHtml($html, $this->url),
                default => $fn(Webpage::withHtml($html, $this->url)),
            };
        }
    }
}