<?php

namespace App\Entity\Selling\Order;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\CurrentPlace;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Project\Product\Product;
use App\Entity\Selling\Customer\BillingAddress;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Selling\Customer\DeliveryAddress;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

 #[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'ref' => 'partial',
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'ref'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'customer' => 'name',
        'currentPlace' => 'name'
    ]),
    ApiResource(
        description: 'Commande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les commandes',
                    'summary' => 'Récupère les commandes',
                ],
                'normalization_context' => [
                    'groups' => ['read:customer', 'read:ref', 'read:current_place', 'read:name']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une commande',
                    'summary' => 'Créer une commande',
                ],
                'denormalization_context' => [
                    'groups' => ['write:customer', 'write:ref', 'write:order:post']
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
            ]
        ],
        shortName: 'SellingOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:ref', 'write:company', 'write:notes', 'write:order', 'write:customer', 'write:current_place', 'write:name'],
            'openapi_definition_name' => 'SellingOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:ref', 'read:company', 'read:notes', 'read:order', 'read:customer', 'read:current_place', 'read:name'],
            'openapi_definition_name' => 'SellingOrder-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'customer_order')
]
class Order extends Entity {
    use CompanyTrait;
    use NotesTrait;
    use RefTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    #[
        ApiProperty(description: 'Référence', required: false, example: 'EJZ65'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Destinataire de la commande', required: false, readableLink: false, example: '/api/billing-addresses/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: BillingAddress::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?BillingAddress $billedTo = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'enabled'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/8'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?Customer $customer = null;

    #[
        ApiProperty(description: 'Destination', required: false, readableLink: false, example: '/api/delivery-addresses/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: DeliveryAddress::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?DeliveryAddress $destination = null;

    #[
        ApiProperty(description: 'Type de produit commandé', required: true, example: Product::KIND_SERIES),
        ORM\Column(type: 'string', nullable: true, options: ['default' => Product::KIND_SERIES]),
        Serializer\Groups(['read:order', 'write:order', 'write:order:post'])
    ]
    private ?string $productOrderKind = Product::KIND_SERIES;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getBilledTo(): ?BillingAddress {
        return $this->billedTo;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getDestination(): ?DeliveryAddress {
        return $this->destination;
    }

    final public function getProductOrderKind(): ?string {
        return $this->productOrderKind;
    }

    final public function setBilledTo(?BillingAddress $billedTo): self {
        $this->billedTo = $billedTo;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
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

    final public function setProductOrderKind(?string $productOrderKind): void {
        $this->productOrderKind = $productOrderKind;
    }
}
