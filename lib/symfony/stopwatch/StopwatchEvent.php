<?php

namespace App\Symfony\Component\Stopwatch;

use Stringable;
use Symfony\Component\Stopwatch\StopwatchEvent as SymfonyStopwatchEvent;

final class StopwatchEvent extends SymfonyStopwatchEvent implements Stringable {
    public function __toString(): string {
        $string = "{$this->getCategory()}: {$this->getStrMemory()} bytes — ";
        if ($this->hasMinutes()) {
            $string .= "{$this->getStrMinutes()}:";
        }
        if ($this->hasSeconds()) {
            $string .= "{$this->getStrSeconds()}:";
        }
        return "$string{$this->getStrMilliseconds()} ms";
    }

    private static function toStr(float $value, int $length = 2): string {
        return str_pad((string) $value, $length, '0', STR_PAD_LEFT);
    }

    private function getMinutes(): float {
        return floor($this->getDuration() / 60000);
    }

    private function getSeconds(): float {
        return floor(($this->getDuration() % 60000) / 1000);
    }

    private function getStrMemory(): string {
        return num_fr_format($this->getMemory(), 0);
    }

    private function getStrMilliseconds(): string {
        return self::toStr(floor($this->getDuration() % 1000), 3);
    }

    private function getStrMinutes(): string {
        return self::toStr($this->getMinutes());
    }

    private function getStrSeconds(): string {
        return self::toStr($this->getSeconds());
    }

    private function hasMinutes(): bool {
        return $this->getMinutes() > 0;
    }

    private function hasSeconds(): bool {
        return $this->getSeconds() > 0;
    }
}
