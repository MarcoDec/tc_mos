<?php

namespace App\Entity\Hr\Employee\Skill;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\EntityId;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\OutTrainer;
use App\Entity\Production\Engine\Engine;
use App\Entity\Production\Engine\Group;
use App\Entity\Project\Product\Product;
use App\Filter\RelationFilter;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['employee']),
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
            'get' => NO_ITEM_GET_OPERATION,
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
            'groups' => ['write:skill'],
            'openapi_definition_name' => 'Skill-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:skill'],
            'openapi_definition_name' => 'Skill-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Skill extends EntityId {
    #[
        ApiProperty(description: 'Employé', readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Employee $employee = null;

    #[
        ApiProperty(description: 'Date de fin', example: '2022-10-03'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DateTimeImmutable $endedDate = null;

    #[
        ApiProperty(description: 'Employé', readableLink: false, example: '/api/engines/3'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Engine $engine = null;

    #[
        ApiProperty(description: 'Groupes d\'équipement', readableLink: false, example: '/api/engine-groups/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Group $family = null;

    #[
        ApiProperty(description: 'Formateur interne', readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Employee $inTrainer = null;

    #[
        ApiProperty(description: 'Niveau', example: 0),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private int $level = 0;

    #[
        ApiProperty(description: 'Formateur extérieur', readableLink: false, example: '/api/out-trainers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?OutTrainer $outTrainer = null;

    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/4'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Activer le suivi du cuivre', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private bool $remindable = false;

    #[
        ApiProperty(description: 'Rappel de l\'enfant', readableLink: false, example: '/api/skills/4'),
        ORM\ManyToOne(targetEntity: self::class),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?self $remindedChild = null;

    #[
        ApiProperty(description: 'Date de rappel', example: '2023-10-03'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DateTimeImmutable $remindedDate = null;

    #[
        ApiProperty(description: 'Date de début', example: '2021-10-03'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?DateTimeImmutable $startedDate = null;

    #[
        ApiProperty(description: 'Type', readableLink: false, example: '/api/skill-types/4'),
        ORM\ManyToOne,
        Serializer\Groups(['read:skill', 'write:skill'])
    ]
    private ?Type $type = null;

    final public function getEmployee(): ?Employee {
        return $this->employee;
    }

    final public function getEndedDate(): ?DateTimeImmutable {
        return $this->endedDate;
    }

    final public function getEngine(): ?Engine {
        return $this->engine;
    }

    final public function getFamily(): ?Group {
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

    final public function getRemindedChild(): ?self {
        return $this->remindedChild;
    }

    final public function getRemindedDate(): ?DateTimeImmutable {
        return $this->remindedDate;
    }

    final public function getStartedDate(): ?DateTimeImmutable {
        return $this->startedDate;
    }

    final public function getType(): ?Type {
        return $this->type;
    }

    final public function isRemindable(): bool {
        return $this->remindable;
    }

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }

    final public function setEndedDate(?DateTimeImmutable $endedDate): self {
        $this->endedDate = $endedDate;
        return $this;
    }

    final public function setEngine(?Engine $engine): self {
        $this->engine = $engine;
        return $this;
    }

    final public function setFamily(?Group $family): self {
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

    final public function setRemindedDate(?DateTimeImmutable $remindedDate): self {
        $this->remindedDate = $remindedDate;
        return $this;
    }

    final public function setStartedDate(?DateTimeImmutable $startedDate): self {
        $this->startedDate = $startedDate;
        return $this;
    }

    final public function setType(?Type $type): self {
        $this->type = $type;
        return $this;
    }
}
