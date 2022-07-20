<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Purchase\Supplier\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Interfaces\WorkflowInterface;
use App\Entity\Management\Currency;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Society\Society;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Fournisseur',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:address', 'read:copper', 'read:current-place', 'read:id', 'read:measure', 'read:supplier:collection'],
                    'openapi_definition_name' => 'Supplier-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les fournisseurs',
                    'summary' => 'Récupère les fournisseurs'
                ]
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
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'transition',
                        'required' => true,
                        'schema' => [
                            'enum' => CurrentPlace::TRANSITIONS,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => null,
                    'summary' => 'Transite le fournisseur à son prochain statut de workflow'
                ],
                'path' => '/suppliers/{id}/promote/{transition}',
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
            'groups' => ['read:address', 'read:copper', 'read:current-place', 'read:id', 'read:measure', 'read:supplier'],
            'openapi_definition_name' => 'Supplier-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Supplier extends Entity implements WorkflowInterface {
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
        ApiProperty(description: 'Statut'),
        ORM\Embedded,
        Serializer\Groups(['read:supplier'])
    ]
    private CurrentPlace $currentPlace;

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
        Serializer\Groups(['create:supplier', 'read:supplier', 'read:supplier:collection', 'write:supplier', 'write:supplier:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:supplier', 'write:supplier', 'write:supplier:main'])
    ]
    private ?string $notes = null;

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
        $this->currentPlace = new CurrentPlace();
    }

    final public function addAdministeredBy(Company $administeredBy): self {
        if (!$this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->add($administeredBy);
            $administeredBy->addSupplier($this);
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

    final public function getConfidenceCriteria(): int {
        return $this->confidenceCriteria;
    }

    final public function getCopper(): Copper {
        return $this->copper;
    }

    final public function getCurrency(): ?Currency {
        return $this->currency;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
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

    final public function getSociety(): ?Society {
        return $this->society;
    }

    #[Pure]
    final public function getState(): ?string {
        return $this->currentPlace->getName();
    }

    #[Pure]
    final public function isDeletable(): bool {
        return $this->currentPlace->isDeletable();
    }

    #[Pure]
    final public function isFrozen(): bool {
        return $this->currentPlace->isFrozen();
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

    final public function setAddress(Address $address): self {
        $this->address = $address;
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

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
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

    final public function setState(?string $state): self {
        $this->currentPlace->setName($state);
        return $this;
    }
}
