<?php

namespace App\Entity\Production\Engine;

use App\Entity\Entity;
use App\Entity\Production\Engine\CounterPart\Group as CounterPartGroup;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\DiscriminatorColumn(name: 'type', type: 'engine_type'),
    ORM\DiscriminatorMap(self::TYPES),
    ORM\Entity,
    ORM\InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'engine_group')
]
abstract class Group extends Entity {
    public const TYPES = [
        'counter-part' => CounterPartGroup::class,
        'tool' => ToolGroup::class,
        'workstation' => WorkstationGroup::class
    ];

    #[
        Assert\Length(min: 2, max: 3),
        Assert\NotBlank,
        ORM\Column(length: 3, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private ?string $code = null;

    #[
        Assert\Length(min: 3, max: 35),
        Assert\NotBlank,
        ORM\Column(length: 35),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    #[
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private bool $safetyDevice = false;

    final public function getCode(): ?string {
        return $this->code;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSafetyDevice(bool $safetyDevice): self {
        $this->safetyDevice = $safetyDevice;
        return $this;
    }
}
