<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Accounting\Bill;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Production\Manufacturing\DeliveryNote\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Selling\Order\Order;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\RefTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

 #[
    ApiResource(
        description: 'Note de livraison',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les notes de livraison',
                    'summary' => 'Récupère les notes de livraison',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une note de livraison',
                    'summary' => 'Créer une note de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une note de livraison',
                    'summary' => 'Supprime une note de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une note de livraison',
                    'summary' => 'Récupère une note de livraison',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une note de livraison',
                    'summary' => 'Modifie une note de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:ref', 'write:company', 'write:delivery-note', 'write:bill', 'write:current_place', 'write:order'],
            'openapi_definition_name' => 'DeliveryNote-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:ref', 'read:company', 'read:delivery-note', 'read:bill', 'read:current_place', 'read:order'],
            'openapi_definition_name' => 'DeliveryNote-read'
        ],
    ),
    ORM\Entity
]
class DeliveryNote extends Entity {
    use CompanyTrait;
    use RefTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Référence'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Facture', required: false, example: '/api/bills/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Bill::class),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?Bill $bill = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'in_creation'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Date', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?DateTimeInterface $date;

    #[
        ApiProperty(description: 'Supplément de transport', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private float $freightSurcharge = 0;

    #[
        ApiProperty(description: 'Non facturable', required: true, example: false),
        ORM\Column(options: ['default' => false], type: 'boolean'),
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private bool $nonBillable = false;

    #[
        ApiProperty(description: 'Commande', required: false, example: '/api/manufacturing-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Order::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?Order $order = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getBill(): ?Bill {
        return $this->bill;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getDate(): ?DateTimeInterface {
        return $this->date;
    }

    final public function getFreightSurcharge(): float {
        return $this->freightSurcharge;
    }

    final public function getOrder(): ?Order {
        return $this->order;
    }

    public function isNonBillable(): bool {
        return $this->nonBillable;
    }

    final public function setBill(?Bill $bill): self {
        $this->bill = $bill;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setDate(?DateTimeInterface $date): self {
        $this->date = $date;
        return $this;
    }

    final public function setFreightSurcharge(float $freightSurcharge): self {
        $this->freightSurcharge = $freightSurcharge;
        return $this;
    }

    public function setNonBillable(bool $nonBillable): void {
        $this->nonBillable = $nonBillable;
    }

    final public function setOrder(?Order $order): self {
        $this->order = $order;
        return $this;
    }
}
