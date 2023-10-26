<?php

namespace App\Entity\Production\Manufacturing;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Production\Manufacturing\Expedition\State;
use App\Entity\Entity;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Selling\Order\Item;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Expédition',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les expéditions',
                    'summary' => 'Récupère les expéditions',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée une expédition',
                    'summary' => 'Crée une expédition',
                ]
            ],
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une expédition',
                    'summary' => 'Supprime une expédition',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une expédition',
                    'summary' => 'Modifie une expédition',
                ]
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le bon à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['expedition','blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le bon à son prochain statut de workflow'
                ],
                'path' => '/expedition/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PRODUCTION_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:expedition', 'write:measure'],
            'openapi_definition_name' => 'Expedition-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:expedition','read:state', 'read:measure'],
            'openapi_definition_name' => 'Expedition-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Expedition extends Entity {
    #[
        ApiProperty(description: 'Numéro de lot', example: '165486543'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?string $batchNumber = null;

    #[
        ApiProperty(description: 'Date', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: false),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private DateTimeImmutable $date;

    #[
        ApiProperty(description: 'Date déterminée ', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?DateTimeImmutable $readyDate = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:engine','read:expedition'])
    ]
    private Blocker $embBlocker;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:expedition'])
    ]
    private State $embState;

    /** @var Item<I>|null */
    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/selling-order-items/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?Item $item = null;

    #[
        ApiProperty(description: 'Localisation', example: 'New York City'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?string $location = null;

    #[
        ApiProperty(description: 'Note de livraison', readableLink: false, example: '/api/delivery-notes/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?DeliveryNote $note = null;

    #[
        ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary']),
        ORM\Embedded,
        Serializer\Groups(['read:measure', 'write:measure'])
    ]
    private Measure $quantity;

    /** @var null|Stock<I> */
    #[
        ApiProperty(description: 'Item', readableLink: false, example: '/api/stocks/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:expedition', 'write:expedition'])
    ]
    private ?Stock $stock = null;

    public function __construct() {
        $this->date = new DateTimeImmutable();
        $this->quantity = new Measure();
        $this->embBlocker = new Blocker();
        $this->embState = new State();

    }

    final public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    final public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    final public function getReadyDate(): ?DateTimeImmutable {
        return $this->readyDate;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    /**
     * @return Item<I>|null
     */
    final public function getItem(): ?Item {
        return $this->item;
    }

    final public function getLocation(): ?string {
        return $this->location;
    }

    final public function getNote(): ?DeliveryNote {
        return $this->note;
    }

    final public function getQuantity(): Measure {
        return $this->quantity;
    }

    /**
     * @return null|Stock<I>
     */
    final public function getStock(): ?Stock {
        return $this->stock;
    }

    /**
     * @return $this
     */
    final public function setBatchNumber(?string $batchNumber): self {
        $this->batchNumber = $batchNumber;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setDate(DateTimeImmutable $date): self {
        $this->date = $date;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setReadyDate(?DateTimeImmutable $readyDate): self {
        $this->readyDate = $readyDate;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    /**
     * @param Item<I>|null $item
     *
     * @return $this
     */
    final public function setItem(?Item $item): self {
        $this->item = $item;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setLocation(?string $location): self {
        $this->location = $location;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setNote(?DeliveryNote $note): self {
        $this->note = $note;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setQuantity(Measure $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param null|Stock<I> $stock
     *
     * @return $this
     */
    final public function setStock(?Stock $stock): self {
        $this->stock = $stock;
        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }

}
