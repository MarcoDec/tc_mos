<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Customer\CurrentPlace;
use App\Entity\Embeddable\Selling\Customer\WebPortal;
use App\Entity\Entity;
use App\Entity\Management\Currency;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\Society\Company;
use App\Entity\Management\Society\Society;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Client',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les clients',
                    'summary' => 'Récupère les clients'
                ],
                'normalization_context' => [
                    'groups' => ['read:id', 'read:name', 'read:customer:collection'],
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un client',
                    'summary' => 'Créer un client'
                ],
                'denormalization_context' => [
                    'groups' => ['write:name', 'write:address', 'write:customer_society', 'write:copper'],
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
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
                'path' => '/customers/{id}/{process}',
                'requirements' => ['process' => '\w+'],
                'openapi_context' => [
                    'description' => 'Modifier un client',
                    'summary' => 'Modifier un client',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'process',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'enum' => ['admin', 'main', 'quality', 'logistics', 'accounting']
                        ]
                    ]],
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ],
            'promote' => [
                'method' => 'PATCH',
                'path' => '/customers/{id}/promote',
                'controller' => PlaceholderAction::class,
                'openapi_context' => [
                    'description' => 'Passer un client à un nouveau statut',
                    'summary' => 'Passer un client à un nouveau statut',
                ],
                'denormalization_context' => [
                    'groups' => ['write:customer:promote']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:customer', 'write:company', 'write:address', 'write:event', 'write:invoice-time-due', 'write:society', 'write:currency', 'write:webportal', 'write:copper'],
            'openapi_definition_name' => 'Customer-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:customer', 'read:company', 'read:address', 'read:event', 'read:invoice-time-due', 'read:society', 'read:currency', 'read:webportal', 'read:copper'],
            'openapi_definition_name' => 'Customer-read'
        ]
    ),
    ORM\Entity
]
class Customer extends Entity {
    #[
        ApiProperty(description: 'Portail de gestion'),
        ORM\Embedded,
        Serializer\Groups(['read:webportal', 'write:webportal'])
    ]
    private WebPortal $accountingPortal;

    #[
        ApiProperty(description: 'Adresse'),
        Assert\Valid(groups: ['Default', 'Society-create']),
        ORM\Embedded,
        Serializer\Groups(['create:society', 'read:society', 'write:society'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/companies/2'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Company $administeredBy = null;

    #[
        ApiProperty(description: 'Temps de livraison', example: 7),
        Assert\PositiveOrZero,
        ORM\Column(type: 'tinyint', options: ['default' => 7, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $conveyanceDuration = 7;

    #[
        ApiProperty(description: 'Cuivre'),
        ORM\Embedded,
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Monnaie', readableLink: false, example: '/api/currencies/2'),
        Assert\NotBlank,
        ORM\ManyToOne,
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private ?Currency $currency;

    #[
        ApiProperty(description: 'Statut'),
        ORM\Embedded,
        Serializer\Groups(['read:customer', 'write:customer', 'read:customer:collection'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Acceptation des équivalents', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $equivalentEnabled = false;

    #[
        ApiProperty(description: 'Factures par email', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $invoiceByEmail = false;

    #[
        ApiProperty(description: 'Langue', example: 'Français'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?string $language = null;

    #[
        ApiProperty(description: 'Encours mensuels', example: '2.66'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private float $monthlyOutstanding = 0;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Kaporingol'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nombre de bons de livraison mensuel', example: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $nbDeliveries = 10;

    #[
        ApiProperty(description: 'Nombre de factures mensuel', example: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $nbInvoices = 10;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Encours maximal souhaité par le client', example: '5'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private float $outstandingMax = 0;

    #[
        ApiProperty(description: 'Date de réglement de la facture', readableLink: false, example: '/api/invoice-time-dues/7'),
        ORM\ManyToOne,
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private ?InvoiceTimeDue $paymentTerms;

    #[
        ApiProperty(description: 'Nouveau statut', example: 'draft'),
        Serializer\Groups(['write:customer:promote'])
    ]
    private ?string $place = null;

    #[
        ApiProperty(description: 'Qualité', required: true, example: 0),
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $quality = 0;

    #[
        ApiProperty(description: 'Société', readableLink: false, example: '/api/societies/2'),
        ORM\ManyToOne,
        Assert\NotBlank,
        Serializer\Groups(['read:subsociety', 'write:subsociety', 'read:society', 'write:society', 'write:customer_society'])
    ]
    private ?Society $society;

    #[
        ApiProperty(description: 'Activer la TVA', required: true, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $vatEnabled = false;

    public function __construct() {
        $this->accountingPortal = new WebPortal();
        $this->address = new Address();
        $this->copper = new Copper();
        $this->currentPlace = new CurrentPlace();
    }

    final public function getAccountingPortal(): WebPortal {
        return $this->accountingPortal;
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getAdministeredBy(): ?Company {
        return $this->administeredBy;
    }

    final public function getConveyanceDuration(): int {
        return $this->conveyanceDuration;
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

    final public function getMonthlyOutstanding(): float {
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

    final public function getOutstandingMax(): float {
        return $this->outstandingMax;
    }

    final public function getPaymentTerms(): ?InvoiceTimeDue {
        return $this->paymentTerms;
    }

    final public function getPlace(): ?string {
        return $this->place;
    }

    final public function getQuality(): int {
        return $this->quality;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    final public function isEquivalentEnabled(): bool {
        return $this->equivalentEnabled;
    }

    final public function isInvoiceByEmail(): bool {
        return $this->invoiceByEmail;
    }

    final public function isVatEnabled(): bool {
        return $this->vatEnabled;
    }

    final public function setAccountingPortal(WebPortal $accountingPortal): self {
        $this->accountingPortal = $accountingPortal;
        return $this;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setAdministeredBy(?Company $administeredBy): self {
        $this->administeredBy = $administeredBy;
        return $this;
    }

    final public function setConveyanceDuration(int $conveyanceDuration): self {
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

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
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

    final public function setMonthlyOutstanding(float $monthlyOutstanding): self {
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

    final public function setOutstandingMax(float $outstandingMax): self {
        $this->outstandingMax = $outstandingMax;
        return $this;
    }

    final public function setPaymentTerms(?InvoiceTimeDue $paymentTerms): self {
        $this->paymentTerms = $paymentTerms;
        return $this;
    }

    final public function setPlace(?string $place): self {
        $this->place = $place;
        return $this;
    }

    final public function setQuality(int $quality): self {
        $this->quality = $quality;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }

    final public function setVatEnabled(bool $vatEnabled): self {
        $this->vatEnabled = $vatEnabled;
        return $this;
    }
}
