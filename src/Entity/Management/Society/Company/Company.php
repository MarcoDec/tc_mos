<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Collection;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Currency;
use App\Entity\Management\Society\Society;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Management\CompanyReference;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Customer\Product as CustomerProduct;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Management\Company\CompanyPatchController;

#[
    ApiResource(
        description: 'Compagnie',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => 'read:company:collection',
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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
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
    #[
        ApiProperty(description: 'Monnaie', readableLink: false, example: '/api/currencies/2'),
        ORM\ManyToOne(targetEntity: Currency::class, fetch: "EAGER"),
        Serializer\Groups(['read:company', 'write:company', 'write:company:selling'])
    ]
    private ?Currency $currency;

    /** @var DoctrineCollection<int, Customer> */
    #[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'administeredBy')]
    private DoctrineCollection $customers;

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
        Serializer\Groups(['read:company', 'write:company', 'write:company:admin'])
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
        Serializer\Groups(['read:company', 'read:company:collection', 'read:printer', 'write:company', 'write:company:admin'])
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
        Serializer\Groups(['read:company', 'write:company', 'write:company:main'])
    ]
    private int $numberOfTeamPerDay = 0;

    /** @var DoctrineCollection<int, CustomerProduct> */
    #[ORM\ManyToMany(targetEntity: CustomerProduct::class, mappedBy: 'administeredBy')]
    private DoctrineCollection $products;

    /** @var DoctrineCollection<int, CompanyReference> */
    #[ORM\ManyToMany(targetEntity: CompanyReference::class, mappedBy: 'items')]
    private DoctrineCollection $references;

    #[
        ApiProperty(description: 'Société'),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'write:company', 'write:company:admin', 'write:company:main'])
    ]
    private ?Society $society = null;

    /** @var DoctrineCollection<int, Supplier> */
    #[ORM\ManyToMany(targetEntity: Supplier::class, mappedBy: 'administeredBy')]
    private DoctrineCollection $suppliers;

    #[
        ApiProperty(description: 'Calendrier de travail', example: '2 jours'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:company', 'write:company', 'write:company:main'])
    ]
    private ?string $workTimetable;

    public function __construct() {
        $this->customers = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->references = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
    }

    final public function addCustomer(Customer $customer): self {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->addAdministeredBy($this);
        }
        return $this;
    }

    final public function addProduct(CustomerProduct $product): self {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addAdministeredBy($this);
        }
        return $this;
    }

    final public function addReference(CompanyReference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->addItem($this);
        }
        return $this;
    }

    final public function addSupplier(Supplier $supplier): self {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
            $supplier->addAdministeredBy($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Check<Component|Product, self>>
     */
    final public function getChecks(): Collection {
        return Collection::collect($this->references->getValues())
            ->map(static function (CompanyReference $reference): Check {
                /** @var Check<Component|Product, self> $check */
                $check = new Check();
                return $check->setReference($reference);
            });
    }

    final public function getCurrency(): ?Currency {
        return $this->currency;
    }

    /**
     * @return DoctrineCollection<int, Customer>
     */
    final public function getCustomers(): DoctrineCollection {
        return $this->customers;
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

    /**
     * @return DoctrineCollection<int, CustomerProduct>
     */
    final public function getProducts(): DoctrineCollection {
        return $this->products;
    }

    /**
     * @return DoctrineCollection<int, CompanyReference>
     */
    final public function getReferences(): DoctrineCollection {
        return $this->references;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    /**
     * @return DoctrineCollection<int, Supplier>
     */
    final public function getSuppliers(): DoctrineCollection {
        return $this->suppliers;
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

    final public function removeCustomer(Customer $customer): self {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            $customer->removeAdministeredBy($this);
        }
        return $this;
    }

    final public function removeProduct(CustomerProduct $product): self {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeAdministeredBy($this);
        }
        return $this;
    }

    final public function removeReference(CompanyReference $reference): self {
        if ($this->references->contains($reference)) {
            $this->references->removeElement($reference);
            $reference->removeItem($this);
        }
        return $this;
    }

    final public function removeSupplier(Supplier $supplier): self {
        if ($this->suppliers->contains($supplier)) {
            $this->suppliers->removeElement($supplier);
            $supplier->removeAdministeredBy($this);
        }
        return $this;
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
