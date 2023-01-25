<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Selling\Customer\State;
use App\Entity\Embeddable\Selling\Customer\WebPortal;
use App\Entity\Entity;
use App\Entity\Management\Currency;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Society\Society;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiResource(
        description: 'Client',
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => ['read:address', 'read:copper', 'read:customer:collection', 'read:id', 'read:measure', 'read:state'],
                    'openapi_definition_name' => 'Customer-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les clients',
                    'summary' => 'Récupère les clients'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:customer', 'write:address', 'write:copper', 'write:measure'],
                    'openapi_definition_name' => 'Customer-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer un client',
                    'summary' => 'Créer un client'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                'validation_groups' => ['Product-create']
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un client',
                    'summary' => 'Supprime un client'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un client',
                    'summary' => 'Récupère un client',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un client',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'enum' => ['accounting', 'admin', 'logistics', 'main'],
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Modifie un client'
                ],
                'path' => '/customers/{id}/{process}',
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                'validation_groups' => AppAssert\ProcessGroupsGenerator::class
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le client à son prochain statut de workflow',
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
                            'schema' => ['enum' => ['customer', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le client à son prochain statut de workflow'
                ],
                'path' => '/customers/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:address', 'write:copper', 'write:customer', 'write:measure'],
            'openapi_definition_name' => 'Customer-write'
        ],
        normalizationContext: [
            'groups' => ['read:address', 'read:copper', 'read:customer', 'read:id', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'Customer-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Customer extends Entity {
    #[
        ApiProperty(description: 'Portail de gestion'),
        ORM\Embedded,
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:accounting'])
    ]
    private WebPortal $accountingPortal;

    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['create:customer', 'read:customer', 'read:customer:collection'])
    ]
    private Address $address;

    /** @var Collection<int, Company> */
    #[
        ApiProperty(description: 'Compagnies dirigeantes', readableLink: false, example: ['/api/companies/1']),
        ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'customers'),
        Serializer\Groups(['read:customer'])
    ]
    private Collection $administeredBy;

    #[
        ApiProperty(description: 'Temps de livraison', openapiContext: ['$ref' => '#/components/schemas/Measure-duration']),
        ORM\Embedded,
        Serializer\Groups(['read:customer'])
    ]
    private Measure $conveyanceDuration;

    #[
        ApiProperty(description: 'Cuivre'),
        ORM\Embedded,
        Serializer\Groups(['create:customer', 'read:customer', 'read:customer:collection'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Monnaie', readableLink: false, example: '/api/currencies/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:customer', 'read:customer', 'write:customer', 'write:customer:accounting'])
    ]
    private ?Currency $currency = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:customer', 'read:customer:collection'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:customer', 'read:customer:collection'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Acceptation des équivalents', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:customer'])
    ]
    private bool $equivalentEnabled = false;

    #[
        ApiProperty(description: 'Factures par email', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:customer'])
    ]
    private bool $invoiceByEmail = false;

    #[
        ApiProperty(description: 'Langue', example: 'Français'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer'])
    ]
    private ?string $language = null;

    #[
        ApiProperty(description: 'Encours mensuels', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:customer'])
    ]
    private Measure $monthlyOutstanding;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Kaporingol'),
        ORM\Column,
        Serializer\Groups(['create:customer', 'read:customer', 'read:customer:collection', 'write:customer', 'write:customer:admin'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nombre de bons de livraison mensuel', example: 10),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:logistics'])
    ]
    private int $nbDeliveries = 10;

    #[
        ApiProperty(description: 'Nombre de factures mensuel', example: 10),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:accounting'])
    ]
    private int $nbInvoices = 10;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:main'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Encours maximal souhaité par le client', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:customer', 'write:customer', 'write:customer:logistics'])
    ]
    private Measure $outstandingMax;

    #[
        ApiProperty(description: 'Date de réglement de la facture', readableLink: false, example: '/api/invoice-time-dues/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:customer', 'read:customer', 'write:customer', 'write:customer:accounting'])
    ]
    private ?InvoiceTimeDue $paymentTerms = null;

    #[
        ApiProperty(description: 'Société', readableLink: false, example: '/api/societies/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['create:customer', 'read:customer', 'read:customer:collection'])
    ]
    private ?Society $society = null;

    public function __construct() {
        $this->accountingPortal = new WebPortal();
        $this->address = new Address();
        $this->administeredBy = new ArrayCollection();
        $this->conveyanceDuration = new Measure();
        $this->copper = new Copper();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
        $this->monthlyOutstanding = new Measure();
        $this->outstandingMax = new Measure();
    }

    final public function addAdministeredBy(Company $administeredBy): self {
        if (!$this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->add($administeredBy);
            $administeredBy->addCustomer($this);
        }
        return $this;
    }

    final public function getAccountingPortal(): WebPortal {
        return $this->accountingPortal;
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

    final public function getConveyanceDuration(): Measure {
        return $this->conveyanceDuration;
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

    final public function getMonthlyOutstanding(): Measure {
        return $this->monthlyOutstanding;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNbDeliveries(): int {
        return $this->nbDeliveries;
    }

    final public function getNbInvoices(): int {
        return $this->nbInvoices;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getOutstandingMax(): Measure {
        return $this->outstandingMax;
    }

    final public function getPaymentTerms(): ?InvoiceTimeDue {
        return $this->paymentTerms;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function isEquivalentEnabled(): bool {
        return $this->equivalentEnabled;
    }

    final public function isInvoiceByEmail(): bool {
        return $this->invoiceByEmail;
    }

    final public function removeAdministeredBy(Company $administeredBy): self {
        if ($this->administeredBy->contains($administeredBy)) {
            $this->administeredBy->removeElement($administeredBy);
            $administeredBy->removeCustomer($this);
        }
        return $this;
    }

    final public function setAccountingPortal(WebPortal $accountingPortal): self {
        $this->accountingPortal = $accountingPortal;
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

    final public function setConveyanceDuration(Measure $conveyanceDuration): self {
        $this->conveyanceDuration = $conveyanceDuration;
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

    final public function setEquivalentEnabled(bool $equivalentEnabled): self {
        $this->equivalentEnabled = $equivalentEnabled;
        return $this;
    }

    final public function setInvoiceByEmail(bool $invoiceByEmail): self {
        $this->invoiceByEmail = $invoiceByEmail;
        return $this;
    }

    final public function setLanguage(?string $language): self {
        $this->language = $language;
        return $this;
    }

    final public function setMonthlyOutstanding(Measure $monthlyOutstanding): self {
        $this->monthlyOutstanding = $monthlyOutstanding;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setNbDeliveries(int $nbDeliveries): self {
        $this->nbDeliveries = $nbDeliveries;
        return $this;
    }

    final public function setNbInvoices(int $nbInvoices): self {
        $this->nbInvoices = $nbInvoices;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setOutstandingMax(Measure $outstandingMax): self {
        $this->outstandingMax = $outstandingMax;
        return $this;
    }

    final public function setPaymentTerms(?InvoiceTimeDue $paymentTerms): self {
        $this->paymentTerms = $paymentTerms;
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
