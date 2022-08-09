<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Accounting\Bill;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Production\Manufacturing\DeliveryNote\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Selling\Order\Order;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Bon de livraison',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les bons de livraison',
                    'summary' => 'Récupère les bons de livraison',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un bon de livraison',
                    'summary' => 'Créer un bon de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un bon de livraison',
                    'summary' => 'Supprime un bon de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un bon de livraison',
                    'summary' => 'Récupère un bon de livraison',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un bon de livraison',
                    'summary' => 'Modifie un bon de livraison',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le bon à son prochain statut de workflow',
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
                    'summary' => 'Transite le bon à son prochain statut de workflow'
                ],
                'path' => '/delivery-notes/{id}/promote/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:delivery-note'],
            'openapi_definition_name' => 'DeliveryNote-write'
        ],
        normalizationContext: [
            'groups' => ['read:current_place', 'read:delivery-note', 'read:id'],
            'openapi_definition_name' => 'DeliveryNote-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class DeliveryNote extends Entity {
    #[
        ApiProperty(description: 'Facture', readableLink: false, example: '/api/bills/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?Bill $bill = null;

    #[
        ApiProperty(description: 'Company', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Statut', example: 'in_creation'),
        ORM\Embedded,
        Serializer\Groups(['read:delivery-note'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Date', readableLink: false, example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?DateTimeImmutable $date = null;

    #[
        ApiProperty(description: 'Supplément de transport', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private Measure $freightSurcharge;

    #[
        ApiProperty(description: 'Non facturable', example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private bool $nonBillable = false;

    #[
        ApiProperty(description: 'Commande', readableLink: false, example: '/api/manufacturing-orders/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?Order $order = null;

    #[
        ApiProperty(description: 'Référence'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:delivery-note', 'write:delivery-note'])
    ]
    private ?string $ref = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
        $this->freightSurcharge = new Measure();
    }

    final public function getBill(): ?Bill {
        return $this->bill;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getDate(): ?DateTimeImmutable {
        return $this->date;
    }

    final public function getFreightSurcharge(): Measure {
        return $this->freightSurcharge;
    }

    final public function getOrder(): ?Order {
        return $this->order;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function isNonBillable(): bool {
        return $this->nonBillable;
    }

    final public function setBill(?Bill $bill): self {
        $this->bill = $bill;
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setDate(?DateTimeImmutable $date): self {
        $this->date = $date;
        return $this;
    }

    final public function setFreightSurcharge(Measure $freightSurcharge): self {
        $this->freightSurcharge = $freightSurcharge;
        return $this;
    }

    final public function setNonBillable(bool $nonBillable): self {
        $this->nonBillable = $nonBillable;
        return $this;
    }

    final public function setOrder(?Order $order): self {
        $this->order = $order;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }
}
