<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Collection;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Society;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Management\CompanyReference;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Customer\Price\Product as CustomerProduct;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Management\Company\CompanyPatchController;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Selling\Customer\Price\Component as ComponentCustomer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'society.id' => 'exact', 'deliveryTime' => 'partial', 'id' => 'exact',
        'deliveryTimeOpenDays' => 'partial', 'engineHourRate' => 'partial', 'generalMargin' => 'partial', 'handlingHourRate' => 'partial',
        'managementFees' => 'partial', 'numberOfTeamPerDay' => 'partial', 'workTimetable' => 'partial'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'workTimetable', 'id']),
    ApiResource(
        description: 'Compagnie',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:id', 'read:company:collection'],
                    'openapi_definition_name' => 'Company-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les compagnies',
                    'summary' => 'Récupère les compagnies',
                ]
            ],
            'options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:company:option'],
                    'openapi_definition_name' => 'Company-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les compagnies pour les select',
                    'summary' => 'Récupère les compagnies pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/companies/options'
            ]
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
                'controller' => CompanyPatchController::class,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Modifie une compagnie',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['admin', 'logistics', 'main', 'selling'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifie une compagnie'
                ],
                'path' => '/companies/{id}/{process}',
                'read' => false,
                'write' => true,
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ]
        ],
//        attributes: [
//            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
//        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:company'],
            'openapi_definition_name' => 'Company-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:company', 'read:id'],
            'openapi_definition_name' => 'Company-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Company extends Entity {
//    #[
//        ApiProperty(description: 'Grilles tarifaires gérées', example: '/api/companies/1'),
//        ORM\OneToMany(mappedBy: 'administeredBy', targetEntity: ComponentCustomer::class),
//        Serializer\Groups(['read:company', 'read:company:collection'])
//        ]
//    private DoctrineCollection $components;

    #[
        ApiProperty(description: 'Temps de livraison', example: 7),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'read:company:collection', 'write:company:logistics'])
    ]
    private int $deliveryTime = 0;

    #[
        ApiProperty(description: 'Est-ce un temps de livraison en jours ouvrés ?', example: true),
        ORM\Column(options: ['default' => true]),
        Serializer\Groups(['read:company', 'write:company', 'read:company:collection', 'write:company:logistics'])
    ]
    private bool $deliveryTimeOpenDays = true;

    #[
        ApiProperty(description: 'Taux horaire machine', example: 27),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'read:company:collection', 'write:company', 'write:company:selling'])
    ]
    private float $engineHourRate = 0;

    #[
        ApiProperty(description: 'Marge générale', example: 2),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'read:company:collection', 'write:company', 'write:company:selling'])
    ]
    private float $generalMargin = 0;

    #[
        ApiProperty(description: 'Taux horaire manutention', example: 15),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'read:company:collection', 'write:company', 'write:company:selling'])
    ]
    private float $handlingHourRate = 0;

    #[
        ApiProperty(description: 'IPv4', example: '255.255.255.254'),
        ORM\Column(length: 15, nullable: true),
        Assert\Ip(version: Assert\Ip::V4),
        Serializer\Groups(['read:company', 'write:company', 'write:company:admin'])
    ]
    private ?string $ip;

    #[
        ApiProperty(description: 'Frais de gestion', example: 15),
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'read:company:collection', 'write:company:selling'])
    ]
    private float $managementFees = 0;

    #[
        ApiProperty(description: 'Nom', example: 'Kaporingol'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:company', 'read:company:collection', 'read:zone', 'write:company', 'write:company:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Texte libre'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:company', 'write:company' , 'write:company:main'])
    ]
    private ?string $notes;

    #[
        ApiProperty(description: 'Nombre de travailleurs dans l\'équipe par jour', example: 4),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:company', 'write:company', 'read:company:collection', 'write:company:main'])
    ]
    private int $numberOfTeamPerDay = 0;

//    /** @var DoctrineCollection<int, CustomerProduct> */
//    #[ORM\OneToMany(mappedBy: 'administeredBy', targetEntity: CustomerProduct::class)]
//    private DoctrineCollection $products;

    #[
        ApiProperty(description: 'Société'),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'read:company:collection', 'write:company', 'write:company:admin', 'write:company:main'])
    ]
    private ?Society $society = null;

    #[
        ApiProperty(description: 'Calendrier de travail', example: '2 jours'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:company', 'read:company:collection', 'write:company', 'write:company:main'])
    ]
    private ?string $workTimetable;

    public function __construct() {
    }

//    final public function addProduct(CustomerProduct $product): self {
//        if (!$this->products->contains($product)) {
//            $this->products->add($product);
//            $product->addAdministeredBy($this);
//        }
//        return $this;
//    }

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

//    /**
//     * @return DoctrineCollection<int, CustomerProduct>
//     */
//    final public function getProducts(): DoctrineCollection {
//        return $this->products;
//    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    #[Serializer\Groups(['read:company:option'])]
    final public function getText(): ?string {
        return $this->getName();
    }

    final public function getWorkTimetable(): ?string {
        return $this->workTimetable;
    }

    final public function isDeliveryTimeOpenDays(): bool {
        return $this->deliveryTimeOpenDays;
    }

//    final public function removeProduct(CustomerProduct $product): self {
//        if ($this->products->contains($product)) {
//            $this->products->removeElement($product);
//            $product->removeAdministeredBy($this);
//        }
//        return $this;
//    }

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

//    /**
//     * @return DoctrineCollection
//     */
//    public function getComponents(): DoctrineCollection
//    {
//        return $this->components;
//    }

//    /**
//     * @param DoctrineCollection $components
//     * @return Company
//     */
//    public function setComponents(DoctrineCollection $components): Company
//    {
//        $this->components = $components;
//        return $this;
//    }

}
