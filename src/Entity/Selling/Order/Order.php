<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Selling\Order\State;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Selling\Customer\BillingAddress;
use App\Entity\Selling\Customer\Contact;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Customer\DeliveryAddress;
use App\Entity\Selling\Order\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\SetFilter;
use App\Filter\RelationFilter;

#[
    ApiFilter(OrderFilter::class, properties: ['id', 'ref', 'createdAt', 'updatedAt']),
    ApiFilter(SetFilter::class, properties: ['embState.state', 'embBlocker.state']),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'ref' => 'partial', 'createdAt' => 'exact', 'updatedAt' => 'exact']),
    ApiFilter(RelationFilter::class, properties: ['billedTo', 'company', 'customer', 'destination']),
    ApiResource(
        description: 'Commande Client',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les commandes',
                    'summary' => 'Récupère les commandes',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une commande',
                    'summary' => 'Créer une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une commande',
                    'summary' => 'Supprime une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une commande',
                    'summary' => 'Récupère une commande',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une commande',
                    'summary' => 'Modifie une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la commande à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Closer::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['selling_order', 'closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite la commande à son prochain statut de workflow'
                ],
                'path' => '/selling-orders/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'SellingOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:order'],
            'openapi_definition_name' => 'SellingOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:order', 'read:state'],
            'openapi_definition_name' => 'SellingOrder-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'selling_order')
]
class Order extends Entity {
    #[
        ApiProperty(description: 'Destinataire de la commande', readableLink: false, example: '/api/billing-addresses/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?BillingAddress $billedTo = null;

    #[
        ApiProperty(description: 'Contact', example: '/api/customer-contacts/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Company', example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/8'),
        ORM\ManyToOne (inversedBy : 'sellingOrders'),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Customer $customer = null;

    #[
        ApiProperty(description: 'Destination', readableLink: false, example: '/api/delivery-addresses/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order', 'read:item'])
    ]
    private ?DeliveryAddress $destination = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:order'])
    ]
    private Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:order'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Ouverte', example: true),
        ORM\Column(type: 'boolean', options: ['default' => true]),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private bool $isOpenOrder = true;

    #[
        ApiProperty(description: 'Type', example: KindType::TYPE_PROTOTYPE, openapiContext: ['enum' => KindType::TYPES]),
        Assert\Choice(choices: KindType::TYPES),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_PROTOTYPE]),
        Serializer\Groups(['read:order', 'write:order', 'read:item'])
    ]
    private ?string $kind = KindType::TYPE_PROTOTYPE;

    const FAMILY_FREE = 'free'; // Commande libre (sans lien avec un produit)
    const FAMILY_FIXED = 'fixed'; // Commande ferme (avec lien avec un produit) mais hors EDI
    const FAMILY_FORECAST = 'forecast'; // Commande de prévisionnelle (avec lien avec un produit) mais hors EDI
    const FAMILY_EDI_ORDERS = 'edi_orders'; // Commande ferme EDI (avec lien avec un produit)
    const FAMILY_EDI_DELFOR = 'edi_delfor'; // Commande de prévisionnelle EDI (avec lien avec un produit)
    const FAMILY_EDI_ORDCHG = 'edi_ordchg'; // Commande de changement de commande EDI (avec lien avec un produit)
    #[
        ApiProperty(description: 'Famille', example: 'fixed', openapiContext: ['enum' => [self::FAMILY_FREE, self::FAMILY_FIXED, self::FAMILY_FORECAST, self::FAMILY_EDI_ORDERS, self::FAMILY_EDI_DELFOR, self::FAMILY_EDI_ORDCHG]]),
        ORM\Column(type: 'string', options: ['default' => self::FAMILY_FIXED]),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private string $orderFamily = '';

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Référence', example: 'EJZ65'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:order', 'write:order', 'read:item'])
    ]
    private ?string $ref = null;

    #[
        ORM\OneToMany(targetEntity: Item::class, mappedBy: 'order')
    ]
    private Collection $sellingOrderItems;

    public function __construct() {
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->sellingOrderItems = new ArrayCollection();
    }

    final public function getBilledTo(): ?BillingAddress {
        return $this->billedTo;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getDestination(): ?DeliveryAddress {
        return $this->destination;
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    public function getSellingOrderItems(): Collection {
        return $this->sellingOrderItems;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function setBilledTo(?BillingAddress $billedTo): self {
        $this->billedTo = $billedTo;
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setDestination(?DeliveryAddress $destination): self {
        $this->destination = $destination;
        return $this;
    }

    final public function setEmbBlocker(Closer $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final public function setSellingOrderItems(Collection $sellingOrderItems): self {
        $this->sellingOrderItems = $sellingOrderItems;

        foreach ($sellingOrderItems as $sellingOrderItem) {
            $sellingOrderItem->setOrder($this);
        }

        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
    }

    public function getOrderFamily(): string
    {
        return $this->orderFamily;
    }

    public function setOrderFamily(string $orderFamily): void
    {
        $this->orderFamily = $orderFamily;
    }

    /**
     * @return bool
     */
    public function isOpenOrder(): bool
    {
        return $this->isOpenOrder;
    }

    /**
     * @param bool $isOpenOrder
     * @return Order
     */
    public function setIsOpenOrder(bool $isOpenOrder): Order
    {
        $this->isOpenOrder = $isOpenOrder;
        return $this;
    }

}
