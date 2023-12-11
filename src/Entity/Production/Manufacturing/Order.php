<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Closer;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Production\Manufacturing\Order\State;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Logistics\Component\Preparation;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Project\Product\Product;
use App\Entity\Selling\Order\Order as SellingOrder;
use App\Entity\Traits\BarCodeTrait;
use App\Filter\RelationFilter;
use App\Filter\SetFilter;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['company']),
    ApiFilter(filterClass: SetFilter::class, properties: ['embState.state','embBlocker.state']),
    ApiResource(
        description: 'OF',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les OF',
                    'summary' => 'Récupère les OF',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un OF',
                    'summary' => 'Créer un OF',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un OF',
                    'summary' => 'Supprime un OF',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un OF',
                    'summary' => 'Récupère un OF',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un OF',
                    'summary' => 'Modifie un OF',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite l\'OF à son prochain statut de workflow',
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
                            'schema' => ['enum' => ['manufacturing_order', 'closer'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite l\'OF à son prochain statut de workflow'
                ],
                'path' => '/manufacturing-orders/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ]
        ],
        shortName: 'ManufacturingOrder',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:manufacturing-order', 'write:measure'],
            'openapi_definition_name' => 'ManufacturingOrder-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:manufacturing-order', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'ManufacturingOrder-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity,
    ORM\Table(name: 'manufacturing_order')
]
class Order extends Entity implements BarCodeInterface {
    use BarCodeTrait;

    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'Date de livraison', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?DateTimeImmutable $deliveryDate = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private Closer $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Index', example: 1),
        ORM\Column(name: '`index`', type: 'tinyint', options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private int $index = 1;

    #[
        ApiProperty(description: 'Compagnie fabricante', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?Company $manufacturingCompany = null;

    #[
        ApiProperty(description: 'Date de production', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?DateTimeImmutable $manufacturingDate = null;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Commande du client', readableLink: false, example: '/api/selling-orders/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order'])
    ]
    private ?SellingOrder $order = null;

    #[
        ApiProperty(description: 'Produit', readableLink: false, example: '/api/products/1'),
        ORM\ManyToOne (inversedBy:'productorders'),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?Product $product = null;

    #[
        ApiProperty(description: 'Quantité demandée', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order','read:manufacturing-operation'])
    ]
    private Measure $quantityRequested;

    #[
        ApiProperty(description: 'Référence', example: '20230320'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:manufacturing-order', 'write:manufacturing-order', 'read:manufacturing-operation'])
    ]
    private ?string $ref = null;

    #[
        ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'order')
    ]
    private Collection $operationOrders;

    #[ORM\OneToMany(targetEntity: Preparation::class, mappedBy: 'ofnumber')]

    private Collection $preparationOrders;

    public function __construct() {
        $this->embBlocker = new Closer();
        $this->embState = new State();
        $this->quantityRequested = new Measure();
        $this->operationOrders = new ArrayCollection();
        $this->preparationOrders = new ArrayCollection();

    }

    public static function getBarCodeTableNumber(): string {
        return self::MANUFACTURING_ORDER_BAR_CODE_PREFIX;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getDeliveryDate(): ?DateTimeImmutable {
        return $this->deliveryDate;
    }

    final public function getEmbBlocker(): Closer {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getIndex(): int {
        return $this->index;
    }

    final public function getManufacturingCompany(): ?Company {
        return $this->manufacturingCompany;
    }

    final public function getManufacturingDate(): ?DateTimeImmutable {
        return $this->manufacturingDate;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getOrder(): ?SellingOrder {
        return $this->order;
    }

    final public function getProduct(): ?Product {
        return $this->product;
    }

    final public function getQuantityRequested(): Measure {
        return $this->quantityRequested;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    public function getOperationOrders(): Collection {
        return $this->operationOrders;
    }

    public function getPreparationOrders(): Collection {
        return $this->preparationOrders;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setDeliveryDate(?DateTimeImmutable $deliveryDate): self {
        $this->deliveryDate = $deliveryDate;
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

    final public function setIndex(int $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setManufacturingCompany(?Company $manufacturingCompany): self {
        $this->manufacturingCompany = $manufacturingCompany;
        return $this;
    }

    final public function setManufacturingDate(?DateTimeImmutable $manufacturingDate): self {
        $this->manufacturingDate = $manufacturingDate;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setOrder(?SellingOrder $order): self {
        $this->order = $order;
        return $this;
    }

    final public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    final public function setQuantityRequested(Measure $quantityRequested): self {
        $this->quantityRequested = $quantityRequested;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
