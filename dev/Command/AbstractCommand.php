<?php

namespace App\Command;

use App\Symfony\Component\Stopwatch\Stopwatch;
use App\Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command {
    /** @var StopwatchEvent[] */
    private array $timers = [];

    final protected function endTime(OutputInterface $output, ?string $name = null): void {
        $output->writeln("<info>{$this->timers[$name ?? $this->getName()]->stop()}</info>");
    }

    final protected function startTime(?string $name = null): void {
        if (!empty($name) || !empty($name = $this->getName())) {
            $this->timers[$name] = (new Stopwatch())->start($name, $name);
        }
    }
}
