<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Purchase\Order\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Purchase\Supplier\Contact;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Selling\Order\Order as CustomerOrder;
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
        'supplier' => 'name',
        'currentPlace' => 'name',
        'deliveryCompany' => 'name'
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
                    'groups' => ['read:supplier', 'read:ref', 'read:current_place', 'read:name', 'read:order:collection']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une commande',
                    'summary' => 'Créer une commande',
                ],
                'denormalization_context' => [
                    'groups' => ['write:supplier', 'write:notes', 'write:order:post']
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
        shortName: 'PurchaseOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:ref', 'write:company', 'write:notes', 'write:order', 'write:customer-contact', 'write:current_place', 'write:name', 'write:supplier'],
            'openapi_definition_name' => 'PurchaseOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:ref', 'read:company', 'read:notes', 'read:order', 'read:customer-contact', 'read:current_place', 'read:name', 'read:supplier'],
            'openapi_definition_name' => 'PurchaseOrder-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'supplier_order')
]
class Order extends Entity {
    use CompanyTrait;
    use NotesTrait;
    use RefTrait;

    #[
        ApiProperty(description: 'Companie', required: false, example: '/api/companies/1'),
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
        ApiProperty(description: 'Contact', required: true, readableLink: false, example: '/api/supplier-contacts/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Contact::class),
        Serializer\Groups(['read:customer-contact', 'write:customer-contact'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'partial_delivery'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Commande du client', required: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: CustomerOrder::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?CustomerOrder $customerOrder = null;

    #[
        ApiProperty(description: 'Compagnie en charge de la livraison', readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company', 'write:order:post', 'read:order:collection'])
    ]
    private ?Company $deliveryCompany;

    #[
        ApiProperty(description: 'Supplément pour le fret ?', required: true, example: true),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:purchase_order', 'write:purchase_order', 'write:order:post'])
    ]
    private bool $supplementFret = false;

    #[
        ApiProperty(description: 'Fournisseur', required: false, readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Supplier::class),
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private ?Supplier $supplier;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getContact(): ?Contact {
        return $this->contact;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getCustomerOrder(): ?CustomerOrder {
        return $this->customerOrder;
    }

    final public function getDeliveryCompany(): ?Company {
        return $this->deliveryCompany;
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function getTrafficLight(): int {
        return $this->currentPlace->getTrafficLight();
    }

    final public function isSupplementFret(): bool {
        return $this->supplementFret;
    }

    final public function setContact(?Contact $contact): self {
        $this->contact = $contact;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setCustomerOrder(?CustomerOrder $customerOrder): self {
        $this->customerOrder = $customerOrder;
        return $this;
    }

    final public function setDeliveryCompany(?Company $deliveryCompany): self {
        $this->deliveryCompany = $deliveryCompany;

        return $this;
    }

    final public function setSupplementFret(bool $supplementFret): self {
        $this->supplementFret = $supplementFret;
        return $this;
    }

    final public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;

        return $this;
    }
}
