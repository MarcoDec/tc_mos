<?php

namespace App\Symfony\Component\Stopwatch;

use App\PrivateInheritanceTrait;
use Symfony\Component\Stopwatch\Section as SymfonySection;

/**
 * @method null|Section get(string $id)
 *
 * @property Section[]        $children
 * @property StopwatchEvent[] $events
 * @property bool             $morePrecision
 * @property float|null       $origin
 */
final class Section extends SymfonySection {
    use PrivateInheritanceTrait;

    public function open($id): self {
        if (null === $id || null === $session = $this->get($id)) {
            $session = $this->children[] = new self(microtime(true) * 1000, $this->morePrecision);
        }
        return $session;
    }

    /**
     * @param string      $name
     * @param null|string $category
     */
    public function startEvent($name, $category): StopwatchEvent {
        if (!isset($this->events[$name])) {
            $events = $this->events;
            $events[$name] = new StopwatchEvent($this->origin ?: microtime(true) * 1000, $category, $this->morePrecision);
            $this->events = $events;
        }
        return $this->events[$name]->start();
    }

    /**
     * @return string[]
     */
    private function getFields(): array {
        return ['children', 'events', 'morePrecision', 'origin'];
    }
}
