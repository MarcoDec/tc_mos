<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Customer\CurrentPlace;
use App\Entity\Embeddable\Selling\Customer\WebPortal;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\Society\Company;
use App\Entity\Management\Society\SubSociety;
use App\Filter\RelationFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

// #[ApiResource]
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
class Customer extends SubSociety {
    #[
        ApiProperty(description: 'Portail de gestion', required: true),
        ORM\Embedded(WebPortal::class),
        Serializer\Groups(['read:webportal', 'write:webportal'])
    ]
    private WebPortal $accountingPortal;

    #[
        ApiProperty(description: 'Compagnie dirigeante', required: true, readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne(targetEntity: Company::class),
        ORM\JoinColumn(nullable: false),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private Company $administeredBy;

    #[
        ApiProperty(description: 'Temps de livraison', required: true, example: 7),
        ORM\Column(type: 'tinyint', options: ['default' => 7, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $conveyanceDuration = 7;

    #[
        ApiProperty(description: 'Cuivre', required: true),
        ORM\Embedded(Copper::class),
        Serializer\Groups(['read:copper', 'write:copper'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Statut', required: true),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:customer', 'write:customer', 'read:customer:collection'])
    ]
    private CurrentPlace $currentPlace;

    /**
     * @var Collection<int, DeliveryAddress>
     */
    #[
        ApiProperty(description: 'Addresse de livraison', required: false, readableLink: false, example: ['/api/delivery-addresses/5', '/api/delivery-addresses/14']),
        ORM\OneToMany(targetEntity: DeliveryAddress::class, mappedBy: 'customer'),
        Serializer\Groups(['read:customer', 'write:customer']),
    ]
    private Collection $deliveryAddress;

    #[
        ApiProperty(description: 'Accepter un équivalent', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $equivalentEnabled = false;

    /**
     * @var Collection<int, Event>
     */
    #[
        ApiProperty(description: 'Evénements', required: false, readableLink: false, example: ['/api/customer-events/5', '/api/customer-eventes/14']),
        ORM\OneToMany(targetEntity: Event::class, mappedBy: 'customer'),
        Serializer\Groups(['read:event', 'write:event']),
    ]
    private Collection $events;

    #[
        ApiProperty(description: 'Accepter les factures par email', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $invoiceByEmail = false;

    #[
        ApiProperty(description: 'Langue', required: false, example: 'Français'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?string $language = null;

    #[
        ApiProperty(description: 'Encours mensuels', required: true, example: '2.66'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private float $monthlyOutstanding = 0;

    #[
        ApiProperty(description: 'Nombre de bons de livraison mensuel', required: true, example: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $nbDeliveries = 10;

    #[
        ApiProperty(description: 'Nombre de factures mensuel', required: true, example: 30),
        ORM\Column(type: 'tinyint', options: ['default' => 10, 'unsigned' => true]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $nbInvoices = 10;

    #[
        ApiProperty(description: 'Notes', required: true, example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Encours maximal souhaité par le client', required: true, example: '5'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private float $outstandingMax = 0;

    #[
        ApiProperty(description: 'Date de réglement de la facture', readableLink: false, required: true, example: '/api/invoice-time-dues/7'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: InvoiceTimeDue::class),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private ?InvoiceTimeDue $paymentTerms;

    #[
        ApiProperty(description: 'Nouveau statut', required: false, example: 'draft'),
        Serializer\Groups(['write:customer:promote'])
    ]
    private ?string $place = null;

    #[
        ApiProperty(description: 'Qualité', required: true, example: 0),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private int $quality = 0;

    #[
        ApiProperty(description: 'Activer la TVA', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private bool $vatEnabled = false;

    final public function __construct() {
        parent::__construct();
        $this->accountingPortal = new WebPortal();
        $this->copper = new Copper();
        $this->currentPlace = new CurrentPlace();
        $this->address = new Address();
        $this->deliveryAddress = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    final public function addDeliveryAddress(DeliveryAddress $deliveryAddress): self {
        if (!$this->deliveryAddress->contains($deliveryAddress)) {
            $this->deliveryAddress[] = $deliveryAddress;
            $deliveryAddress->setCustomer($this);
        }

        return $this;
    }

    final public function addEvent(Event $event): self {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setCustomer($this);
        }

        return $this;
    }

    final public function getAccountingPortal(): WebPortal {
        return $this->accountingPortal;
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

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    /**
     * @return Collection|DeliveryAddress[]
     */
    final public function getDeliveryAddress(): Collection {
        return $this->deliveryAddress;
    }

    final public function getEquivalentEnabled(): ?bool {
        return $this->equivalentEnabled;
    }

    /**
     * @return Collection|Event[]
     */
    final public function getEvents(): Collection {
        return $this->events;
    }

    final public function getInvoiceByEmail(): ?bool {
        return $this->invoiceByEmail;
    }

    final public function getLanguage(): ?string {
        return $this->language;
    }

    final public function getMonthlyOutstanding(): ?float {
        return $this->monthlyOutstanding;
    }

    final public function getNbDeliveries() {
        return $this->nbDeliveries;
    }

    final public function getNbInvoices() {
        return $this->nbInvoices;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getOutstandingMax(): ?float {
        return $this->outstandingMax;
    }

    final public function getPaymentTerms(): ?InvoiceTimeDue {
        return $this->paymentTerms;
    }

    final public function getPlace(): ?string {
        return $this->place;
    }

    final public function getQuality(): ?int {
        return $this->quality;
    }

    final public function getVatEnabled(): ?bool {
        return $this->vatEnabled;
    }

    final public function removeDeliveryAddress(DeliveryAddress $deliveryAddress): self {
        if ($this->deliveryAddress->removeElement($deliveryAddress)) {
            // set the owning side to null (unless already changed)
            if ($deliveryAddress->getCustomer() === $this) {
                $deliveryAddress->setCustomer(null);
            }
        }

        return $this;
    }

    final public function removeEvent(?Event $event): self {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            if ($event->getCustomer() === $this) {
                $event->setCustomer(null);
            }
        }
        return $this;
    }

    final public function setAccountingPortal(WebPortal $accountingPortal): self {
        $this->accountingPortal = $accountingPortal;

        return $this;
    }

    final public function setAdministeredBy(?Company $administeredBy): self {
        $this->administeredBy = $administeredBy;

        return $this;
    }

    final public function setConveyanceDuration($conveyanceDuration): self {
        $this->conveyanceDuration = $conveyanceDuration;

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

    final public function setNbDeliveries($nbDeliveries): self {
        $this->nbDeliveries = $nbDeliveries;

        return $this;
    }

    final public function setNbInvoices($nbInvoices): self {
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

    final public function setVatEnabled(bool $vatEnabled): self {
        $this->vatEnabled = $vatEnabled;

        return $this;
    }
}
