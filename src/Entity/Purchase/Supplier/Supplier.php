<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Purchase\Supplier\CurrentPlace;
use App\Entity\Management\Society\Company;
use App\Entity\Management\Society\SubSociety;
use App\Entity\Quality\Reception\Reference;
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'name' => 'partial'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'currentPlace' => 'name'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'name'
    ]),
    ApiResource(
        description: 'Fournisseur',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les fournisseurs',
                    'summary' => 'Récupère les fournisseurs'
                ],
                'normalization_context' => [
                    'groups' => ['read:id', 'read:name', 'read:supplier:collection'],
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un fournisseur',
                    'summary' => 'Créer un fournisseur'
                ],
                'denormalization_context' => [
                    'groups' => ['write:name', 'write:address', 'write:copper', 'write:society', 'write:currency'],
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
                'path' => '/suppliers/{id}/{process}',
                'requirements' => ['process' => '\w+'],
                'openapi_context' => [
                    'description' => 'Modifier un fournisseur',
                    'summary' => 'Modifier un fournisseur',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['admin', 'main', 'quality', 'purchase-logistics', 'accounting']
                        ]
                    ]],
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ],
            'promote' => [
                'method' => 'PATCH',
                'path' => '/suppliers/{id}/promote',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Passer un fournisseur à un nouveau statut',
                    'summary' => 'Passer un fournisseur à un nouveau statut',
                ],
                'denormalization_context' => [
                    'groups' => ['write:supplier:promote']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:supplier', 'write:company', 'write:address', 'write:supplier', 'write:society', 'write:currency', 'write:webportal', 'write:copper'],
            'openapi_definition_name' => 'supplier-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:supplier', 'read:company', 'read:address', 'read:supplier', 'read:society', 'read:currency', 'read:webportal', 'read:copper'],
            'openapi_definition_name' => 'supplier-read'
        ]
    ),
    ORM\Entity
]
class Supplier extends SubSociety {
    #[
        ApiProperty(description: 'Compagnie dirigeante', required: true, readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne(targetEntity: Company::class),
        ORM\JoinColumn(nullable: false),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private Company $administeredBy;

    #[
        ApiProperty(description: 'Critère de confiance', required: true, example: 0),
        ORM\Column(type: 'tinyint', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private int $confidenceCriteria = 0;

    #[
        ApiProperty(description: 'Cuivre', required: true),
        ORM\Embedded(Copper::class),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Statut', required: true),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:supplier', 'write:supplier', 'read:supplier:collection'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Langue', required: false, example: 'Français'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private ?string $language = null;

    #[
        ApiProperty(description: 'Gestion de la production', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private bool $managedProduction = false;

    #[
        ApiProperty(description: 'Gestion de la qualité', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private bool $managedQuality = false;

    #[
        ApiProperty(description: 'Notes', required: true, example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Nouveau statut', required: false, example: 'draft'),
        Serializer\Groups(['write:supplier:promote'])
    ]
    private ?string $place = null;

    /**
     * @var Collection<int, Reference>
     */
    #[
        ApiProperty(description: 'Références', readableLink: false, example: ['/api/references/2', '/api/references/18']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Reference::class, mappedBy: 'suppliers'),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private Collection $references;

    final public function __construct() {
        parent::__construct();
        $this->copper = new Copper();
        $this->currentPlace = new CurrentPlace();
        $this->references = new ArrayCollection();
    }

    final public function addReference(Reference $reference): self {
        if (!$this->references->contains($reference)) {
            $this->references[] = $reference;
            $reference->addSupplier($this);
        }

        return $this;
    }

    final public function getAdministeredBy(): Company {
        return $this->administeredBy;
    }

    final public function getConfidenceCriteria(): int {
        return $this->confidenceCriteria;
    }

    final public function getCopper(): Copper {
        return $this->copper;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getLanguage(): ?string {
        return $this->language;
    }

    final public function getManagedProduction(): ?bool {
        return $this->managedProduction;
    }

    final public function getManagedQuality(): ?bool {
        return $this->managedQuality;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getPlace(): ?string {
        return $this->place;
    }

    /**
     * @return Collection<int, Reference>
     */
    final public function getReferences(): Collection {
        return $this->references;
    }

    final public function removeReference(Reference $reference): self {
        if ($this->references->removeElement($reference)) {
            $reference->removeSupplier($this);
        }

        return $this;
    }

    final public function setAdministeredBy(Company $administeredBy): self {
        $this->administeredBy = $administeredBy;

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

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;

        return $this;
    }

    final public function setPlace(?string $place): self {
        $this->place = $place;

        return $this;
    }
}
