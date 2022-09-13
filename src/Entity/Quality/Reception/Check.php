<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Quality\Reception\CheckController;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Quality\Reception\State;
use App\Entity\EntityId;
use App\Entity\Logistics\Order\Receipt;
use App\Entity\Quality\Reception\Reference\Reference;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 * @template C of \App\Entity\Management\Society\Company\Company|\App\Entity\Purchase\Component\Component|\App\Entity\Purchase\Component\Family|\App\Entity\Purchase\Supplier\Supplier|\App\Entity\Project\Product\Family|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Contrôle',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contrôles',
                    'summary' => 'Récupère les contrôles'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée un contrôle',
                    'summary' => 'Crée un contrôle'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ],
            'receipt' => [
                'controller' => CheckController::class,
                'deserialize' => false,
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Contrôle une réception',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'id',
                        'required' => true,
                        'schema' => ['type' => 'integer']
                    ]],
                    'requestBody' => null,
                    'summary' => 'Contrôle une réception'
                ],
                'path' => '/checks/receipt/{id}',
                'read' => false,
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')',
                'validate' => false,
                'write' => false
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite le contrôle à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => State::TRANSITIONS, 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['check'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite le le contrôle à son prochain statut de workflow'
                ],
                'path' => '/checks/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_QUALITY_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:check'],
            'openapi_definition_name' => 'Check-write'
        ],
        normalizationContext: [
            'groups' => ['read:check', 'read:id', 'read:state'],
            'openapi_definition_name' => 'Check-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table(name: '`check`')
]
class Check extends EntityId {
    #[
        ORM\Embedded,
        Serializer\Groups(['read:check'])
    ]
    private State $embState;

    /** @var null|Receipt<I> */
    #[
        ApiProperty(description: 'Reçu', readableLink: false, example: '/api/receipts/1'),
        ORM\ManyToOne(cascade: ['persist'], inversedBy: 'checks'),
        Serializer\Groups(['read:check', 'write:check'])
    ]
    private ?Receipt $receipt = null;

    /** @var null|Reference<C> */
    #[
        ApiProperty(description: 'Référence', readableLink: false, example: '/api/references/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:check', 'write:check'])
    ]
    private ?Reference $reference = null;

    public function __construct() {
        $this->embState = new State();
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    /**
     * @return null|Receipt<I>
     */
    final public function getReceipt(): ?Receipt {
        return $this->receipt;
    }

    /**
     * @return null|Reference<C>
     */
    final public function getReference(): ?Reference {
        return $this->reference;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    /**
     * @return $this
     */
    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    /**
     * @param null|Receipt<I> $receipt
     *
     * @return $this
     */
    final public function setReceipt(?Receipt $receipt): self {
        $this->receipt = $receipt;
        return $this;
    }

    /**
     * @param null|Reference<C> $reference
     *
     * @return $this
     */
    final public function setReference(?Reference $reference): self {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }
}
