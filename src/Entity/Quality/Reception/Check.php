<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Logistics\Order\Receipt;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @template F of \App\Entity\Purchase\Component\Family|\App\Entity\Project\Product\Family
 * @template I of \App\Entity\Purchase\Component\Component|\App\Entity\Project\Product\Product
 */
#[
    ApiResource(
        description: 'Contrôle',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contrôles',
                    'summary' => 'Récupère les contrôles',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Crée un contrôle',
                    'summary' => 'Crée un contrôle',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ],
        ],
        itemOperations: [
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un contrôle',
                    'summary' => 'Modifie un contrôle',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_WRITER.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un contrôle',
                    'summary' => 'Supprime un contrôle',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_QUALITY_ADMIN.'\')'
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
            'groups' => ['read:id', 'read:check'],
            'openapi_definition_name' => 'Check-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Check extends Entity {
    /** @var null|Receipt<I> */
    #[
        ApiProperty(description: 'Reçu', readableLink: false, example: '/api/receipts/5'),
        ORM\ManyToOne,
        Serializer\Groups(['read:check', 'write:check'])
    ]
    private ?Receipt $receipt = null;

    /** @var null|Reference<F, I> */
    #[
        ApiProperty(description: 'Référence', readableLink: false, example: '/api/references/2'),
        ORM\ManyToOne,
        Serializer\Groups(['read:check', 'write:check'])
    ]
    private ?Reference $reference = null;

    /**
     * @return null|Receipt<I>
     */
    final public function getReceipt(): ?Receipt {
        return $this->receipt;
    }

    /**
     * @return null|Reference<F, I>
     */
    final public function getReference(): ?Reference {
        return $this->reference;
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
     * @param null|Reference<F, I> $reference
     *
     * @return $this
     */
    final public function setReference(?Reference $reference): self {
        $this->reference = $reference;
        return $this;
    }
}
