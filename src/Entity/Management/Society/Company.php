<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Management\Currency;
use App\Entity\Quality\Reception\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
class Company extends SubSociety {
    #[
        ApiProperty(description: 'Monnaie', required: true, readableLink: false, example: '/api/currencies/2'),
        Assert\NotBlank,
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    protected ?Currency $currency;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Kaporingol'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'read:company:collection'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Société', required: true, readableLink: false, example: '/api/societies/2'),
        Assert\NotBlank,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    protected ?Society $society;

    #[
        ApiProperty(description: 'Temps de livraison', required: true, example: 7),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private int $deliveryTime = 0;

    #[
        ApiProperty(description: 'Est-ce un temps de livraison en jours ouvrés ?', required: true, example: true),
        ORM\Column(type: 'boolean', options: ['default' => true]),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private bool $deliveryTimeOpenDays = true;

    #[
        ApiProperty(description: 'Taux horaire machine', required: true, example: 27),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private float $engineHourRate = 0;

    #[
        ApiProperty(description: 'Marge générale', required: true, example: 2),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private float $generalMargin = 0;

    #[
        ApiProperty(description: 'Taux horaire manutention', required: true, example: 15),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private float $handlingHourRate = 0;

    #[
        ApiProperty(description: 'IPv4', required: true, example: '255.255.255.254'),
        ORM\Column(type: 'string', length: 15, nullable: true),
        Assert\Ip(version: Assert\Ip::V4),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $ip;

    #[
        ApiProperty(description: 'Frais de gestion', required: true, example: 15),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private float $managementFees = 0;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Texte libre'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Assert\Ip(version: Assert\Ip::V4),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $notes;

    #[
        ApiProperty(description: 'Nombre de travailleurs dans l\'équipe par jour', required: false, example: 4),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private int $numberOfTeamPerDay = 0;

    /**
     * @var Collection<int, Reference>
     */
    #[
        ApiProperty(description: 'Références', readableLink: false, example: ['/api/references/2', '/api/references/18']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Reference::class, mappedBy: 'companies'),
        Serializer\Groups(['read:company'])
    ]
    private Collection $references;

    #[
        ApiProperty(description: 'Calendrier de travail', required: false, example: '2 jours'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?string $workTimetable;

    public function __construct() {
        parent::__construct();
        $this->references = new ArrayCollection();
    }

    final public function addReference(Reference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->addCompany($this);
        }
        return $this;
    }

    final public function getDeliveryTime(): int {
        return $this->deliveryTime;
    }

    final public function getDeliveryTimeOpenDays(): ?bool {
        return $this->deliveryTimeOpenDays;
    }

    final public function getEngineHourRate(): ?float {
        return $this->engineHourRate;
    }

    final public function getGeneralMargin(): ?float {
        return $this->generalMargin;
    }

    final public function getHandlingHourRate(): ?float {
        return $this->handlingHourRate;
    }

    final public function getIp(): ?string {
        return $this->ip;
    }

    final public function getManagementFees(): ?float {
        return $this->managementFees;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getNumberOfTeamPerDay(): int {
        return $this->numberOfTeamPerDay;
    }

    /**
     * @return Collection<int, Reference>
     */
    final public function getReferences(): Collection {
        return $this->references;
    }

    final public function getWorkTimetable(): ?string {
        return $this->workTimetable;
    }

    final public function removeReference(Reference $reference): self {
        if ($this->references->contains($reference)) {
            $this->references->removeElement($reference);
            $reference->removeCompany($this);
        }
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

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;

        return $this;
    }

    final public function setNumberOfTeamPerDay(int $numberOfTeamPerDay): self {
        $this->numberOfTeamPerDay = $numberOfTeamPerDay;

        return $this;
    }

    final public function setWorkTimetable(?string $workTimetable): self {
        $this->workTimetable = $workTimetable;

        return $this;
    }
}
