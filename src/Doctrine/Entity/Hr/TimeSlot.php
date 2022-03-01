<?php

namespace App\Doctrine\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\Entity\Embeddable\Hr\Employee\Roles;
use App\Doctrine\Entity\Entity;
use App\Doctrine\Entity\Traits\NameTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use const NO_ITEM_GET_OPERATION;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['end' => 'partial', 'endBreak' => 'partial', 'name' => 'partial', 'start' => 'partial', 'startBreak' => 'partial']),
    ApiResource(
        description: 'Plages horaires',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les plages horaires',
                    'summary' => 'Récupère les plages horaires',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une plage horaire',
                    'summary' => 'Créer une plage horaire',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une plage horaire',
                    'summary' => 'Supprime une plage horaire',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une plage horaire',
                    'summary' => 'Modifie une plage horaire',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:time-slot', 'write:name'],
            'openapi_definition_name' => 'TimeSlot-write'
        ],
        normalizationContext: [
            'groups' => ['read:time-slot', 'read:id', 'read:name'],
            'openapi_definition_name' => 'TimeSlot-read'
        ]
    ),
    ORM\Entity,
    ORM\Table
]
class TimeSlot extends Entity {
    use NameTrait;

    #[
        ApiProperty(description: 'Nom', example: 'Journée'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    protected ?string $name = null;

    /**
     * @ORM\Column(type="time_immutable")
     */
    #[
        ApiProperty(description: 'Fin', example: '17:30:00'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $end = null;

    #[
        ApiProperty(description: 'Fin pause', example: '13:30:00'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $endBreak = null;

    #[
        ApiProperty(description: 'Début', example: '07:30:00'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $start = null;

    #[
        ApiProperty(description: 'Début pause', example: '12:30:00'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?DateTimeImmutable $startBreak = null;

    final public function getEnd(): ?DateTimeImmutable {
        return $this->end;
    }

    final public function getEndBreak(): ?DateTimeImmutable {
        return $this->endBreak;
    }

    final public function getStart(): ?DateTimeImmutable {
        return $this->start;
    }

    final public function getStartBreak(): ?DateTimeImmutable {
        return $this->startBreak;
    }

    final public function setEnd(?DateTimeImmutable $end): self {
        $this->end = $end;
        return $this;
    }

    final public function setEndBreak(?DateTimeImmutable $endBreak): self {
        $this->endBreak = $endBreak;
        return $this;
    }

    final public function setStart(?DateTimeImmutable $start): self {
        $this->start = $start;
        return $this;
    }

    final public function setStartBreak(?DateTimeImmutable $startBreak): self {
        $this->startBreak = $startBreak;
        return $this;
    }
}
