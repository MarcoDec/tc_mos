<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Production\Manufacturing\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Management\Society\Company;
use App\Entity\Project\Product\Product;
use App\Entity\Selling\Order\Order as CustomerOrder;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
use App\Filter\RelationFilter;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'ref' => 'partial',
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'ref', 'quantityProduced', 'quantityRequested', 'deliveryDate'
    ]),
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'product' => 'name',
        'currentPlace' => 'name'
    ]),
    ApiFilter(filterClass: DateFilter::class, properties: [
        'deliveryDate'
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
                    'groups' => ['read:ref', 'read:product', 'read:current_place', 'read:name', 'read:manufacturing_order:collection']
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une commande',
                    'summary' => 'Créer une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une commande',
                    'summary' => 'Supprime une commande',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
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
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        shortName: 'ManufacturingOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:ref', 'write:company', 'write:notes', 'write:manufacturing_order', 'write:current_place', 'write:order', 'write:name', 'write:product'],
            'openapi_definition_name' => 'ManufacturingOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:ref', 'read:company', 'read:notes', 'read:manufacturing_order', 'read:current_place', 'read:order', 'read:name', 'read:product'],
            'openapi_definition_name' => 'ManufacturingOrder-read'
        ],
    ),
    ORM\Entity,
    ORM\Table(name: 'manufacturing_order')
]
class Order extends Entity implements BarCodeInterface {
    use BarCodeTrait;
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
        ApiProperty(description: 'Quantité actuelle', required: true, example: 0),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order'])
    ]
    private int $actualQuantity = 0;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'confirmed'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Date de livraison', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:order', 'write:order', 'read:manufacturing_order:collection'])
    ]
    private ?DateTimeInterface $deliveryDate;

    #[
        ApiProperty(description: 'Index', required: true, example: 1),
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order'])
    ]
    private int $index = 1;

    #[
        ApiProperty(description: 'Companie fabricante', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    private ?Company $manufacturingCompany = null;

    #[
        ApiProperty(description: 'Date de production', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order'])
    ]
    private ?DateTimeInterface $manufacturingDate;

    #[
        ApiProperty(description: 'Commande du client', required: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: CustomerOrder::class),
        Serializer\Groups(['read:order', 'write:order'])
    ]
    private ?CustomerOrder $order = null;

    #[
        ApiProperty(description: 'Produit', required: true, readableLink: false, example: '/api/products/4'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Product::class),
        Serializer\Groups(['read:product', 'write:product'])
    ]
    private ?Product $product;

    #[
        ApiProperty(description: 'Quantité produite', required: true, example: 50),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order', 'read:manufacturing_order:collection'])
    ]
    private int $quantityProduced = 0;

    #[
        ApiProperty(description: 'Quantité demandée', required: true, example: 100),
        ORM\Column(type: 'integer', options: ['default' => 0, 'unsigned' => true]),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order', 'read:manufacturing_order:collection'])
    ]
    private int $quantityRequested = 0;

    #[
        ApiProperty(description: 'Date de validation', required: false, example: '2022-12-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:manufacturing_order', 'write:manufacturing_order'])
    ]
    private ?DateTimeInterface $validationDate;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    public static function getBarCodeTableNumber(): string {
        return self::MANUFACTURING_ORDER_BAR_CODE_PREFIX;
    }

    final public function getActualQuantity(): int {
        return $this->actualQuantity;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getDeliveryDate(): ?DateTimeInterface {
        return $this->deliveryDate;
    }

    final public function getIndex(): int {
        return $this->index;
    }

    final public function getManufacturingCompany(): ?Company {
        return $this->manufacturingCompany;
    }

    final public function getManufacturingDate(): ?DateTimeInterface {
        return $this->manufacturingDate;
    }

    final public function getOrder(): ?CustomerOrder {
        return $this->order;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getQuantityProduced(): int {
        return $this->quantityProduced;
    }

    final public function getQuantityRequested(): int {
        return $this->quantityRequested;
    }

    final public function getTrafficLight(): int {
        return $this->currentPlace->getTrafficLight();
    }

    final public function getValidationDate(): ?DateTimeInterface {
        return $this->validationDate;
    }

    final public function setActualQuantity(int $actualQuantity): self {
        $this->actualQuantity = $actualQuantity;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setDeliveryDate(?DateTimeInterface $deliveryDate): self {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    final public function setIndex(int $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setManufacturingCompany(?Company $manufacturingCompany): self {
        $this->manufacturingCompany = $manufacturingCompany;
        return $this;
    }

    final public function setManufacturingDate(?DateTimeInterface $manufacturingDate): self {
        $this->manufacturingDate = $manufacturingDate;
        return $this;
    }

    final public function setOrder(?CustomerOrder $order): self {
        $this->order = $order;
        return $this;
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setQuantityProduced(int $quantityProduced): self {
        $this->quantityProduced = $quantityProduced;
        return $this;
    }

    final public function setQuantityRequested(int $quantityRequested): self {
        $this->quantityRequested = $quantityRequested;
        return $this;
    }

    final public function setValidationDate(?DateTimeInterface $validationDate): self {
        $this->validationDate = $validationDate;
        return $this;
    }
}
