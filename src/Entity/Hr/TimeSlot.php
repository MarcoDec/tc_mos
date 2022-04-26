<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['end', 'endBreak', 'name', 'start', 'startBreak']),
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une plage horaire',
                    'summary' => 'Supprime une plage horaire',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une plage horaire',
                    'summary' => 'Modifie une plage horaire',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:time-slot', 'write:name'],
            'openapi_definition_name' => 'TimeSlot-write'
        ],
        normalizationContext: [
            'groups' => ['read:time-slot', 'read:id', 'read:name'],
            'openapi_definition_name' => 'TimeSlot-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table
]
class TimeSlot extends Entity {
    /**
     * @ORM\Column(type="time_immutable")
     */
    #[
        ApiProperty(description: 'Fin', example: '17:30:00'),
        ORM\Column(type: 'time_immutable'),
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
        ApiProperty(description: 'Nom', example: 'Journée'),
        Assert\NotBlank,
        ORM\Column(length: 10),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Début', example: '07:30:00'),
        ORM\Column(type: 'time_immutable'),
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

    final public function getName(): ?string {
        return $this->name;
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

    final public function setName(?string $name): self {
        $this->name = $name;
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
