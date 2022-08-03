<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Currency;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name',
    ]),
    ApiResource(
        description: 'Compagnie',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les compagnies',
                    'summary' => 'Récupère les compagnies',
                ],
                'normalization_context' => [
                    'groups' => 'read:company:collection'
                ]
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une compagnie',
                    'summary' => 'Supprime une compagnie',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une compagnie',
                    'summary' => 'Récupère une compagnie'
                ]
            ],
            'patch' => [
                'path' => '/companies/{id}/{process}',
                'requirements' => ['process' => '\w+'],
                'openapi_context' => [
                    'description' => 'Modifier une compagnie',
                    'summary' => 'Modifier une compagnie',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['admin', 'main', 'selling', 'logistics']
                        ]
                    ]]
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ],
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:company', 'write:name', 'write:society', 'write:currency'],
            'openapi_definition_name' => 'Company-write'
        ],
        normalizationContext: [
            'groups' => ['read:company', 'read:name', 'read:society', 'read:currency'],
            'openapi_definition_name' => 'Company-read'
        ],
    ),
    ORM\Entity
]
class Company extends Entity {
    #[
        ApiProperty(description: 'Monnaie', readableLink: false, example: '/api/currencies/2'),
        Assert\NotBlank,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Currency $currency;

    #[
        ApiProperty(description: 'Temps de livraison', example: 7),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'write:company:logistics'])
    ]
    private int $deliveryTime = 0;

    #[
        ApiProperty(description: 'Est-ce un temps de livraison en jours ouvrés ?', example: true),
        ORM\Column(options: ['default' => true]),
        Serializer\Groups(['read:company', 'write:company', 'write:company:logistics'])
    ]
    private bool $deliveryTimeOpenDays = true;

    #[
        ApiProperty(description: 'Taux horaire machine', example: 27),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'write:company:selling'])
    ]
    private float $engineHourRate = 0;

    #[
        ApiProperty(description: 'Marge générale', example: 2),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'write:company:selling'])
    ]
    private float $generalMargin = 0;

    #[
        ApiProperty(description: 'Taux horaire manutention', example: 15),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'write:company:selling'])
    ]
    private float $handlingHourRate = 0;

    #[
        ApiProperty(description: 'IPv4', example: '255.255.255.254'),
        ORM\Column(length: 15, nullable: true),
        Assert\Ip(version: Assert\Ip::V4),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $ip;

    #[
        ApiProperty(description: 'Frais de gestion', example: 15),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'write:company:selling'])
    ]
    private float $managementFees = 0;

    #[
        ApiProperty(description: 'Nom', example: 'Kaporingol'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:company', 'write:company', 'write:company:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Texte libre'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $notes;

    #[
        ApiProperty(description: 'Nombre de travailleurs dans l\'équipe par jour', example: 4),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private int $numberOfTeamPerDay = 0;

    #[
        ApiProperty(description: 'Société', readableLink: false, example: '/api/societies/2'),
        Assert\NotBlank,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Society $society;

    #[
        ApiProperty(description: 'Calendrier de travail', example: '2 jours'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $workTimetable;

    final public function getCurrency(): ?Currency {
        return $this->currency;
    }

    final public function getDeliveryTime(): int {
        return $this->deliveryTime;
    }

    final public function getEngineHourRate(): float {
        return $this->engineHourRate;
    }

    final public function getGeneralMargin(): float {
        return $this->generalMargin;
    }

    final public function getHandlingHourRate(): float {
        return $this->handlingHourRate;
    }

    final public function getIp(): ?string {
        return $this->ip;
    }

    final public function getManagementFees(): float {
        return $this->managementFees;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getNumberOfTeamPerDay(): int {
        return $this->numberOfTeamPerDay;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    final public function getWorkTimetable(): ?string {
        return $this->workTimetable;
    }

    final public function isDeliveryTimeOpenDays(): bool {
        return $this->deliveryTimeOpenDays;
    }

    final public function setCurrency(?Currency $currency): self {
        $this->currency = $currency;
        return $this;
    }

    final public function setDeliveryTime(int $deliveryTime): self {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    final public function setDeliveryTimeOpenDays(bool $deliveryTimeOpenDays): self {
        $this->deliveryTimeOpenDays = $deliveryTimeOpenDays;
        return $this;
    }

    final public function setEngineHourRate(float $engineHourRate): self {
        $this->engineHourRate = $engineHourRate;
        return $this;
    }

    final public function setGeneralMargin(float $generalMargin): self {
        $this->generalMargin = $generalMargin;
        return $this;
    }

    final public function setHandlingHourRate(float $handlingHourRate): self {
        $this->handlingHourRate = $handlingHourRate;
        return $this;
    }

    final public function setIp(?string $ip): self {
        $this->ip = $ip;
        return $this;
    }

    final public function setManagementFees(float $managementFees): self {
        $this->managementFees = $managementFees;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setNumberOfTeamPerDay(int $numberOfTeamPerDay): self {
        $this->numberOfTeamPerDay = $numberOfTeamPerDay;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }

    final public function setWorkTimetable(?string $workTimetable): self {
        $this->workTimetable = $workTimetable;
        return $this;
    }
}
