<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CronJobRepository;
use Cron\CronExpression;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CronJobRepository::class)]
class CronJob extends Entity {
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $last = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $next;

    public function __construct(
        #[ORM\Column(type: 'char', length: 20)] private string $command,
        #[ORM\Column(type: 'char', length: 6)] private string $period
    ) {
        $this->setNext();
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getLast(): ?DateTimeImmutable {
        return $this->last;
    }

    public function getNext(): DateTimeImmutable {
        return $this->next;
    }

    public function isDue(): bool {
        if ($this->getCronExpression()->isDue()) {
            return true;
        }
        return $this->last === null;
    }

    public function run(): self {
        $this->last = new DateTimeImmutable();
        $this->setNext();
        return $this;
    }

    public function setPeriod(string $period): self {
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
