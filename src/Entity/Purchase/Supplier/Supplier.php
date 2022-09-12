<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Purchase\Supplier\State;
use App\Entity\EntityId;
use App\Entity\Management\Currency;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Society\Society;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\Order;
use App\Entity\Quality\Reception\Check;
use App\Entity\Quality\Reception\Reference\Purchase\SupplierReference;
use App\Repository\Purchase\Supplier\SupplierRepository;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as LaravelCollection;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Fournisseur',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:address', 'read:copper', 'read:id', 'read:measure', 'read:state', 'read:supplier:collection'],
                    'openapi_definition_name' => 'Supplier-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les fournisseurs',
                    'summary' => 'Récupère les fournisseurs'
                ]
            ],
            'get-receipts' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:state', 'read:supplier:receipt'],
                    'openapi_definition_name' => 'Supplier-receipt',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les fournisseurs dont les commandes attendent une réception',
                    'summary' => 'Récupère les fournisseurs dont les commandes attendent une réception'
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/suppliers/get-receipts'
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:supplier', 'write:address', 'write:copper', 'write:measure'],
                    'openapi_definition_name' => 'Supplier-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un fournisseur',
                    'summary' => 'Créer un fournisseur'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un fournisseur',
                    'summary' => 'Supprime un fournisseur'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un fournisseur',
                    'summary' => 'Récupère un fournisseur',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un fournisseur',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['accounting', 'admin', 'main', 'purchase-logistics', 'quality'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifie un fournisseur'
                ],
                'path' => '/suppliers/{id}/{process}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le fournisseur à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['supplier', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le fournisseur à son prochain statut de workflow'
                ],
                'path' => '/suppliers/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:copper', 'write:measure', 'write:supplier'],
            'openapi_definition_name' => 'Supplier-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:copper', 'read:id', 'read:measure', 'read:state', 'read:supplier'],
            'openapi_definition_name' => 'Supplier-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity(repositoryClass: SupplierRepository::class)
]
class Supplier extends EntityId {
    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['create:supplier', 'read:supplier', 'read:supplier:collection'])
    ]
    private Address $address;

    /** @var Collection<int, Company> */
    #[
        ApiProperty(description: 'Compagnies dirigeantes', readableLink: false, example: ['/api/companies/1']),
        ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'suppliers'),
        Serializer\Groups(['read:supplier'])
    ]
    private Collection $administeredBy;

    #[
        ApiProperty(description: 'Critère de confiance', example: 0),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:supplier', 'read:supplier:collection', 'write:supplier', 'write:supplier:main'])
    ]
    private int $confidenceCriteria = 0;

    #[
        ApiProperty(description: 'Cuivre'),
        ORM\Embedded,
        Serializer\Groups(['create:supplier', 'read:supplier', 'read:supplier:collection', 'write:supplier', 'write:purchase-logistics'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Monnaie', readableLink: false, example: '/api/currencies/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:supplier', 'read:supplier', 'write:supplier', 'write:supplier:accounting'])
    ]
    private ?Currency $currency = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:supplier', 'read:supplier:collection', 'read:supplier:receipt'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:supplier', 'read:supplier:collection'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Langue', example: 'Français'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:supplier'])
    ]
    private ?string $language = null;

    #[
        ApiProperty(description: 'Gestion de la production', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:supplier', 'write:supplier', 'write:supplier:purchase-logistics'])
    ]
    private bool $managedProduction = false;

    #[
        ApiProperty(description: 'Gestion de la qualité', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:supplier', 'write:supplier', 'write:supplier:quality'])
    ]
    private bool $managedQuality = false;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Kaporingol'),
        ORM\Column,
        Serializer\Groups(['create:supplier', 'read:supplier', 'read:supplier:collection', 'read:supplier:receipt', 'write:supplier', 'write:supplier:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:supplier', 'write:supplier', 'write:supplier:main'])
    ]
    private ?string $notes = null;

    /** @var Collection<int, Order> */
    #[
        ORM\OneToMany(mappedBy: 'supplier', targetEntity: Order::class),
        Serializer\Groups(['read:supplier:receipt'])
    ]
    private Collection $orders;

    /** @var Collection<int, SupplierReference> */
    #[ORM\ManyToMany(targetEntity: SupplierReference::class, mappedBy: 'items')]
    private Collection $references;

    #[
        ApiProperty(description: 'Société', readableLink: false, example: '/api/societies/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:supplier', 'read:supplier', 'read:supplier:collection'])
    ]
    private ?Society $society = null;

    public function __construct() {
        $this->administeredBy = new ArrayCollection();
        $this->address = new Address();
        $this->copper = new Copper();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
        $this->orders = new ArrayCollection();
        $this->references = new ArrayCollection();
    }

    final public function addAdministeredBy(Company $administeredBy): self {
        if (!$this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->add($administeredBy);
            $administeredBy->addSupplier($this);
        }
        return $this;
    }

    final public function addOrder(Order $order): self {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setSupplier($this);
        }
        return $this;
    }

    final public function addReference(SupplierReference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->addItem($this);
        }
        return $this;
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    /**
     * @return Collection<int, Company>
     */
    final public function getAdministeredBy(): Collection {
        return $this->administeredBy;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    /**
     * @return LaravelCollection<int, Check<Component|Product, self>>
     */
    final public function getChecks(): LaravelCollection {
        return collect($this->references->getValues())
            ->map(static function (SupplierReference $reference): Check {
                /** @var Check<Component|Product, self> $check */
                $check = new Check();
                return $check->setReference($reference);
            })
            ->values();
    }

    final public function getConfidenceCriteria(): int {
        return $this->confidenceCriteria;
    }

    final public function getCopper(): Copper {
        return $this->copper;
    }

    final public function getCurrency(): ?Currency {
        return $this->currency;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getLanguage(): ?string {
        return $this->language;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    /**
     * @return Collection<int, Order>
     */
    final public function getOrders(): Collection {
        return $this->orders;
    }

    /**
     * @return Collection<int, SupplierReference>
     */
    final public function getReferences(): Collection {
        return $this->references;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function isManagedProduction(): bool {
        return $this->managedProduction;
    }

    final public function isManagedQuality(): bool {
        return $this->managedQuality;
    }

    final public function removeAdministeredBy(Company $administeredBy): self {
        if ($this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->removeElement($administeredBy);
            $administeredBy->removeSupplier($this);
        }
        return $this;
    }

    final public function removeOrder(Order $order): self {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            if ($order->getSupplier() === $this) {
                $order->setSupplier(null);
            }
        }
        return $this;
    }

    final public function removeReference(SupplierReference $reference): self {
        if ($this->references->contains($reference)) {
            $this->references->removeElement($reference);
            $reference->removeItem($this);
        }
        return $this;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setConfidenceCriteria(int $confidenceCriteria): self {
        $this->confidenceCriteria = $confidenceCriteria;
        return $this;
    }

    final public function setCopper(Copper $copper): self {
        $this->copper = $copper;
        return $this;
    }

    final public function setCurrency(?Currency $currency): self {
        $this->currency = $currency;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setLanguage(?string $language): self {
        $this->language = $language;
        return $this;
    }

    final public function setManagedProduction(bool $managedProduction): self {
        $this->managedProduction = $managedProduction;
        return $this;
    }

    final public function setManagedQuality(bool $managedQuality): self {
        $this->managedQuality = $managedQuality;
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

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
