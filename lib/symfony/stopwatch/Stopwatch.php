<?php

namespace App\Symfony\Component\Stopwatch;

use App\PrivateInheritanceTrait;
use Symfony\Component\Stopwatch\Stopwatch as SymfonyStopwatch;

/**
 * @method StopwatchEvent start(string $name, string $category = null)
 *
 * @property Section[] $activeSections
 * @property bool      $morePrecision
 * @property Section[] $sections
 */
final class Stopwatch extends SymfonyStopwatch {
    use PrivateInheritanceTrait;

    public function reset(): void {
        $this->sections = $this->activeSections = ['__root__' => new Section(null, $this->morePrecision)];
    }

    /**
     * @return string[]
     */
    private function getFields(): array {
        return ['activeSections', 'morePrecision', 'sections'];
    }
}
