<?php

namespace App\Entity;

use App\Repository\CronJobRepository;
use Cron\CronExpression;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CronJobRepository::class)]
class CronJob extends Entity {
    #[ORM\Column(length: 18)]
    private string $command;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $last = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $next;

    #[ORM\Column(length: 6)]
    private string $period;

    final public function __construct(string $command, string $period) {
        $this->command = $command;
        $this->period = $period;
        $this->setNext();
    }

    final public function getNext(): DateTimeImmutable {
        return $this->next;
    }

    final public function isDue(): bool {
        return $this->getCronExpression()->isDue();
    }

    final public function run(): self {
        $this->last = new DateTimeImmutable();
        $this->setNext();
        return $this;
    }

    final public function setPeriod(string $period): self {
        $this->period = $period;
        return $this;
    }

    private function getCronExpression(): CronExpression {
        return new CronExpression($this->period);
    }

    private function setNext(): self {
        $this->next = DateTimeImmutable::createFromMutable($this->getCronExpression()->getNextRunDate());
        return $this;
    }
}
