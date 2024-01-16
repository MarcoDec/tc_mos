<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Filter\NumericFilter;
use App\Repository\Management\InvoiceTimeDueRepository;
use App\Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['endOfMonth']),
    ApiFilter(filterClass: NumericFilter::class, properties: ['days', 'daysAfterEndOfMonth']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Délai de paiement des factures',
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
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un délai de paiement des factures',
                    'summary' => 'Supprime un délai de paiement des factures',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un délai de paiement des factures',
                    'summary' => 'Modifie un délai de paiement des factures',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:invoice-time-due'],
            'openapi_definition_name' => 'InvoiceTimeDue-write'
        ],
        normalizationContext: [
            'groups' => ['read:invoice-time-due', 'read:id'],
            'openapi_definition_name' => 'InvoiceTimeDue-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: InvoiceTimeDueRepository::class),
    UniqueEntity(['days', 'daysAfterEndOfMonth', 'endOfMonth']),
    UniqueEntity('name')
]
class InvoiceTimeDue extends Entity {
    #[
        ApiProperty(description: 'Jours ', example: 30),
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private int $days = 0;

    #[
        ApiProperty(description: 'Jours après la fin du mois ', example: 0),
        Assert\Length(min: 0, max: 31),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private int $daysAfterEndOfMonth = 0;

    #[
        ApiProperty(description: 'Fin du mois ', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private bool $endOfMonth = false;

    #[
        ApiProperty(description: 'Nom', required: true, example: '30 jours fin de mois'),
        ORM\Column(length: 40),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due']),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank
    ]
    private ?string $name = null;

    final public function getDays(): int {
        return $this->days;
    }

    final public function getDaysAfterEndOfMonth(): int {
        return $this->daysAfterEndOfMonth;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function isEndOfMonth(): bool {
        return $this->endOfMonth;
    }

    final public function setDays(int $days): self {
        $this->days = $days;
        return $this;
    }

    final public function setDaysAfterEndOfMonth(int $daysAfterEndOfMonth): self {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;
        $this->setEndOfMonth($this->endOfMonth);
        return $this;
    }

    final public function setEndOfMonth(bool $endOfMonth): self {
        $this->endOfMonth = $endOfMonth || $this->daysAfterEndOfMonth > 0;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
