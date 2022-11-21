<?php

declare(strict_types=1);

namespace App\Entity\Management;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Entity;
use App\Filter\NumericFilter;
use App\Filter\OrderFilter;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: BooleanFilter::class, properties: ['endOfMonth']),
    ApiFilter(filterClass: NumericFilter::class, properties: ['days', 'daysAfterEndOfMonth']),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Délai de paiement des factures',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les délais', 'summary' => 'Récupère les délais']
            ),
            new Post(
                openapiContext: ['description' => 'Créer un délai', 'summary' => 'Créer un délai'],
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un délai', 'summary' => 'Supprime un délai'],
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un délai', 'summary' => 'Modifie un délai'],
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'invoice-time-due-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'invoice-time-due-read'
        ],
        denormalizationContext: ['groups' => ['invoice-time-due-write']],
        order: ['name' => 'asc'],
        security: Role::GRANTED_MANAGEMENT_ADMIN
    ),
    ORM\Entity,
    UniqueEntity(
        fields: ['days', 'daysAfterEndOfMonth', 'deleted', 'endOfMonth'],
        message: 'The combination is already used.',
        ignoreNull: false
    ),
    UniqueEntity(fields: ['name', 'deleted'], ignoreNull: false)
]
class InvoiceTimeDue extends Entity {
    #[
        ApiProperty(description: 'Jours ', example: 30),
        Assert\Range(min: 0, max: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['invoice-time-due-read', 'invoice-time-due-write'])
    ]
    private int $days = 0;

    #[
        ApiProperty(description: 'Jours après la fin du mois ', example: 15),
        Assert\Range(min: 0, max: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['invoice-time-due-read', 'invoice-time-due-write'])
    ]
    private int $daysAfterEndOfMonth = 0;

    #[
        ApiProperty(description: 'Fin du mois ', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['invoice-time-due-read', 'invoice-time-due-write'])
    ]
    private bool $endOfMonth = false;

    #[
        ApiProperty(description: 'Nom', required: true, example: '30 jours fin de mois'),
        Assert\Length(min: 3, max: 40),
        Assert\NotBlank,
        ORM\Column(length: 40),
        Serializer\Groups(['invoice-time-due-read', 'invoice-time-due-write'])
    ]
    private ?string $name = null;

    public function getDays(): int {
        return $this->days;
    }

    public function getDaysAfterEndOfMonth(): int {
        return $this->daysAfterEndOfMonth;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function isEndOfMonth(): bool {
        return $this->endOfMonth;
    }

    public function setDays(int $days): self {
        $this->days = $days;
        return $this;
    }

    public function setDaysAfterEndOfMonth(int $daysAfterEndOfMonth): self {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;
        $this->setEndOfMonth($this->endOfMonth);
        return $this;
    }

    public function setEndOfMonth(bool $endOfMonth): self {
        $this->endOfMonth = $endOfMonth || $this->daysAfterEndOfMonth > 0;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
