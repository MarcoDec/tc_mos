<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\OutTrainer;
use App\Entity\Production\Engine\Engine;
use App\Entity\Production\Engine\Group;
use App\Entity\Project\Product\Product;
use App\Filter\RelationFilter;
use DatetimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'employee' => 'name',
    ]),
    ApiResource(
        description: 'Compétence',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les compétences',
                    'summary' => 'Récupère les compétences',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une compétence',
                    'summary' => 'Créer une compétence',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une compétence',
                    'summary' => 'Supprime une compétence',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une compétence',
                    'summary' => 'Modifie une compétence',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:employee', 'write:skill', 'write:engine', 'write:engine-group', 'write:out-trainer', 'write:out-trainer', 'write:product', ' write:engine'],
            'openapi_definition_name' => 'Skill-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:employee', 'read:skill', 'read:engime', 'read:engine-group', 'read:out-trainer', 'read:product', 'read:engine'],
            'openapi_definition_name' => 'Skill-read'
        ],
    ),
    ORM\Entity
]
class Skill extends Entity {
    #[
        ApiProperty(description: 'Employé', required: false, readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class, inversedBy: 'skills'),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?Employee $employee;

    #[
        ApiProperty(description: 'Date de fin', required: false, example: '2052-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DatetimeInterface $endedDate = null;

    #[
        ApiProperty(description: 'Employé', required: true, readableLink: false, example: '/api/manufacturer-engines/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Engine::class),
        Serializer\Groups(['read:engine', 'write:engine'])
    ]
    private Engine $engine;

    #[
        ApiProperty(description: 'Groupes d\'équipement', required: true, readableLink: false, example: '/api/engine-groups/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Group::class),
        Serializer\Groups(['read:engine-group', 'write:engine-group'])
    ]
    private Group $family;

    #[
        ApiProperty(description: 'Formateur interne', required: false, readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?Employee $inTrainer = null;

    #[
        ApiProperty(description: 'Niveau', required: true, example: 0),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private int $level = 0;

    #[
        ApiProperty(description: 'Formateur extérieur', required: false, readableLink: false, example: '/api/out-trainers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: OutTrainer::class),
        Serializer\Groups(['read:out-trainer', 'write:out-trainer'])
    ]
    private ?OutTrainer $outTrainer = null;

    #[
        ApiProperty(description: 'Produit', required: true, readableLink: false, example: '/api/products/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Activer le suivi du cuivre', required: true, example: false),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private bool $remindable = false;

    #[
        ApiProperty(description: 'Rappel de l\'enfant', required: true, readableLink: false, example: '/api/skills/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: self::class),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?self $remindedChild = null;

    #[
        ApiProperty(description: 'Date de rappel', required: false, example: '2052-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DatetimeInterface $remindedDate = null;

    #[
        ApiProperty(description: 'Date de début', required: false, example: '2052-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DatetimeInterface $startedDate = null;

    #[
        ApiProperty(description: 'Type', required: true, readableLink: false, example: '/api/skill-types/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Type::class),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Type $type = null;

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEndedDate(): ?DateTimeInterface {
        return $this->endedDate;
    }

    final public function getEngine(): Engine {
        return $this->engine;
    }

    final public function getFamily(): Group {
        return $this->family;
    }

    final public function getInTrainer(): ?Employee {
        return $this->inTrainer;
    }

    final public function getLevel(): int {
        return $this->level;
    }

    final public function getOutTrainer(): ?OutTrainer {
        return $this->outTrainer;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getRemindable(): ?bool {
        return $this->remindable;
    }

    final public function getRemindedChild(): ?self {
        return $this->remindedChild;
    }

    final public function getRemindedDate(): ?DateTimeInterface {
        return $this->remindedDate;
    }

    final public function getStartedDate(): ?DateTimeInterface {
        return $this->startedDate;
    }

    final public function getType(): ?Type {
        return $this->type;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;

        return $this;
    }

    final public function setEndedDate(?DateTimeInterface $endedDate): self {
        $this->endedDate = $endedDate;

        return $this;
    }

    final public function setEngine(Engine $engine): self {
        $this->engine = $engine;

        return $this;
    }

    final public function setFamily(Group $family): self {
        $this->family = $family;

        return $this;
    }

    final public function setInTrainer(?Employee $inTrainer): self {
        $this->inTrainer = $inTrainer;

        return $this;
    }

    final public function setLevel(int $level): self {
        $this->level = $level;

        return $this;
    }

    final public function setOutTrainer(?OutTrainer $outTrainer): self {
        $this->outTrainer = $outTrainer;

        return $this;
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;

        return $this;
    }

    final public function setRemindable(bool $remindable): self {
        $this->remindable = $remindable;

        return $this;
    }

    final public function setRemindedChild(?self $remindedChild): self {
        $this->remindedChild = $remindedChild;

        return $this;
    }

    final public function setRemindedDate(?DateTimeInterface $remindedDate): self {
        $this->remindedDate = $remindedDate;

        return $this;
    }

    final public function setStartedDate(?DateTimeInterface $startedDate): self {
        $this->startedDate = $startedDate;

        return $this;
    }

    final public function setType(?Type $type): self {
        $this->type = $type;

        return $this;
    }
}
