<?php

namespace App\Entity\Production\Engine\Machine;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Production\Engine\Engine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Machines',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les machines',
                    'summary' => 'Récupère les machines'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une machine',
                    'summary' => 'Créer une machine'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
        ],
        itemOperations: ['get', 'patch', 'delete'],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:engine'],
            'openapi_definition_name' => 'Machine-write'
        ],
        normalizationContext: [
            'enable_max_depth' => true,
            'groups' => ['read:engine', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Machine-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Machine extends Engine {
    #[
        ApiProperty(description: 'Groupe', readableLink: false, example: '/api/machines/1'),
        ORM\ManyToOne(targetEntity: Group::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    protected $group;

    #[
        ApiProperty(description: 'Machine parente', readableLink: false, example: '/api/machines/1'),
        ORM\ManyToOne(targetEntity: Machine::class, inversedBy: 'subMachines'),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ?Machine $parentMachine;
    #[
        ApiProperty(description: 'Sous-machines', readableLink: false, example: '/api/machines/1'),
        ORM\OneToMany( mappedBy: 'parentMachine', targetEntity: Machine::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private ArrayCollection $subMachines;

    public function __construct() {
        parent::__construct();
        $this->subMachines = new ArrayCollection();
    }

    public function getSubMachines(): ArrayCollection
    {
        return $this->subMachines;
    }
    public function addSubMachine(Machine $subMachine): self
    {
        if (!$this->subMachines->contains($subMachine)) {
            $this->subMachines[] = $subMachine;
            $subMachine->setParentMachine($this);
        }
        return $this;
    }
    public function removeSubMachine(Machine $subMachine): self
    {
        if ($this->subMachines->removeElement($subMachine)) {
            if ($subMachine->getParentMachine() === $this) {
                $subMachine->setParentMachine(null);
            }
        }
        return $this;
    }
    public function getParentMachine(): ?Machine
    {
        return $this->parentMachine;
    }
    public function setParentMachine(?Machine $parentMachine): self
    {
        $this->parentMachine = $parentMachine;
        return $this;
    }
}
