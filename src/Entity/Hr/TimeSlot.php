<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Entity;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Plage horaire.
 */
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
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
        denormalizationContext: [
            'groups' => ['TimeSlot-write'],
            'openapi_definition_name' => 'TimeSlot-write'
        ],
        normalizationContext: [
            'groups' => ['TimeSlot-read'],
            'openapi_definition_name' => 'TimeSlot-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['TimeSlot-read' => ['TimeSlot-write', 'Entity']], write: ['TimeSlot-write']),
    ORM\Entity,
    UniqueEntity('name')
]
class TimeSlot extends Entity {
    /**
     * @var DateTimeImmutable|null Fin
     */
    #[
        ApiProperty(example: '17:30:00', format: 'endTime'),
        ORM\Column(type: 'time_immutable'),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(groups: ['TimeSlot-read', 'TimeSlot-write'])
    ]
    private ?DateTimeImmutable $end = null;

    /**
     * @var DateTimeImmutable|null Fin de la pause
     */
    #[
        ApiProperty(example: '13:30:00', format: 'endTime'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(groups: ['TimeSlot-read', 'TimeSlot-write'])
    ]
    private ?DateTimeImmutable $endBreak = null;

    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: 'Journée', format: 'name'),
        Assert\NotBlank,
        ORM\Column(length: 10),
        Serializer\Groups(groups: ['TimeSlot-read', 'TimeSlot-write'])
    ]
    private ?string $name = null;

    /**
     * @var DateTimeImmutable|null Début
     */
    #[
        ApiProperty(example: '07:30:00', format: 'startTime'),
        ORM\Column(type: 'time_immutable'),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(groups: ['TimeSlot-read', 'TimeSlot-write'])
    ]
    private ?DateTimeImmutable $start = null;

    /**
     * @var DateTimeImmutable|null Début de la pause
     */
    #[
        ApiProperty(example: '12:30:00', format: 'startTime'),
        ORM\Column(type: 'time_immutable', nullable: true),
        Serializer\Context([DateTimeNormalizer::FORMAT_KEY => 'H:i:s']),
        Serializer\Groups(groups: ['TimeSlot-read', 'TimeSlot-write'])
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
