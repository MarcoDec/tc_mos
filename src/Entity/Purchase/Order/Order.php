<?php

namespace App\Entity\Purchase\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Purchase\Supplier\Contact;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Selling\Order\Order as CustomerOrder;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Commande',
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
            ]
        ],
        shortName: 'SupplierOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:order'],
            'openapi_definition_name' => 'PurchaseOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:order'],
            'openapi_definition_name' => 'PurchaseOrder-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'supplier_order')
]
class Order extends Entity {
    #[
        ApiProperty(description: 'Companie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Contact', readableLink: false, example: '/api/supplier-contacts/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:customer-contact', 'write:customer-contact'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Commande du client', readableLink: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?CustomerOrder $customerOrder = null;

    #[
        ApiProperty(description: 'Compagnie en charge de la livraison', readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne,
        Serializer\Groups(['read:company', 'write:company', 'write:order:post', 'read:order:collection'])
    ]
    private ?Company $deliveryCompany = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Référence', example: 'EJZ65'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    private ?string $ref = null;

    #[
        ApiProperty(description: 'Supplément pour le fret', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:purchase_order', 'write:purchase_order', 'write:order:post'])
    ]
    private bool $supplementFret = false;

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier', 'write:supplier'])
    ]
    private ?Supplier $supplier = null;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getContact(): ?Contact {
        return $this->contact;
    }

    final public function getCustomerOrder(): ?CustomerOrder {
        return $this->customerOrder;
    }

    final public function getDeliveryCompany(): ?Company {
        return $this->deliveryCompany;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    final public function isSupplementFret(): bool {
        return $this->supplementFret;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setContact(?Contact $contact): self {
        $this->contact = $contact;
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

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
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
