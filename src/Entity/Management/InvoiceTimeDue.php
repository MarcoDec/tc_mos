<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Entity\Entity;
use App\Filter\NumericFilter;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Délai de paiement des factures.
 */
#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['endOfMonth']),
    ApiFilter(filterClass: NumericFilter::class, properties: ['days', 'daysAfterEndOfMonth']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les délais de paiement des factures',
                    'summary' => 'Récupère les délais de paiement des factures',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un délai de paiement des factures',
                    'summary' => 'Créer un délai de paiement des factures',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un délai de paiement des factures',
                    'summary' => 'Supprime un délai de paiement des factures',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un délai de paiement des factures',
                    'summary' => 'Modifie un délai de paiement des factures',
                ]
            ]
        ],
        denormalizationContext: [
            'groups' => ['InvoiceTimeDue-write'],
            'openapi_definition_name' => 'InvoiceTimeDue-write'
        ],
        normalizationContext: [
            'groups' => ['InvoiceTimeDue-read'],
            'openapi_definition_name' => 'InvoiceTimeDue-read'
        ]
    ),
    ApiSerializerGroups(inheritedRead: ['InvoiceTimeDue-read' => ['InvoiceTimeDue-write', 'Entity']], write: ['InvoiceTimeDue-write']),
    ORM\Entity,
    UniqueEntity(['days', 'daysAfterEndOfMonth', 'endOfMonth']),
    UniqueEntity('name')
]
class InvoiceTimeDue extends Entity {
    /**
     * @var int|null Jours
     */
    #[
        ApiProperty(example: 30),
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(groups: ['InvoiceTimeDue-read', 'InvoiceTimeDue-write'])
    ]
    private ?int $days = 0;

    /**
     * @var int|null Jours après la fin du mois
     */
    #[
        ApiProperty,
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(groups: ['InvoiceTimeDue-read', 'InvoiceTimeDue-write'])
    ]
    private ?int $daysAfterEndOfMonth = 0;

    /**
     * @var bool|null Fin du mois
     */
    #[
        ApiProperty(example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(groups: ['InvoiceTimeDue-read', 'InvoiceTimeDue-write'])
    ]
    private ?bool $endOfMonth = false;

    /**
     * @var null|string Nom
     */
    #[
        ApiProperty(example: '30 jours fin de mois', format: 'name'),
        ORM\Column(length: 30),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        Serializer\Groups(groups: ['InvoiceTimeDue-read', 'InvoiceTimeDue-write'])
    ]
    private ?string $name = null;

    final public function getDays(): ?int {
        return $this->days;
    }

    final public function getDaysAfterEndOfMonth(): ?int {
        return $this->daysAfterEndOfMonth;
    }

    final public function getEndOfMonth(): ?bool {
        return $this->endOfMonth;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function setDays(?int $days): self {
        $this->days = $days;
        return $this;
    }

    final public function setDaysAfterEndOfMonth(?int $daysAfterEndOfMonth): self {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;
        return $this;
    }

    final public function setEndOfMonth(?bool $endOfMonth): self {
        $this->endOfMonth = $endOfMonth;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
