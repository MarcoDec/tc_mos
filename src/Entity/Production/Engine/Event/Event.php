<?php

namespace App\Entity\Production\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Maintenance\Engine\Event\CurrentPlace;
use App\Entity\Event as AbstractEvent;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Maintenance\Engine\Event\Maintenance;
use App\Entity\Maintenance\Engine\Event\Request;
use App\Entity\Production\Engine\Engine;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ORM\Entity,
    ORM\DiscriminatorColumn(name: 'type', type: 'string'),
    ORM\DiscriminatorMap(Event::TYPES),
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_event'),
    ORM\MappedSuperclass
]
abstract class Event extends AbstractEvent {
    public const TYPES = ['maintenance' => Maintenance::class, 'request' => Request::class];

    #[
        ApiProperty(description: 'Employé', required: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    protected ?Employee $employee;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'closed'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Machine', required: true, readableLink: false, example: '/api/manufacturer-engines/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Engine::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?Engine $engine;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    public function getType(): string {
        return 'event';
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }
}
