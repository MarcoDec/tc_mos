<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Logistics\Order\Receipt;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

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
            'openapi_definition_name' => 'Check-read'
        ],
    ),
    ORM\Entity
]
class Check extends Entity {
    #[
        ApiProperty(description: 'Reçu', required: false, example: '/api/receipts/5'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Receipt::class),
        Serializer\Groups(['read:check', 'write:check'])
    ]
    private Receipt $receipt;

    #[
        ApiProperty(description: 'Référence', required: false, example: '/api/references/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Reference::class),
    ]
    private ?Reference $reference;

    public function getReceipt(): Receipt {
        return $this->receipt;
    }

    public function getReference(): Reference {
        return $this->reference;
    }

    public function setReceipt(Receipt $receipt): self {
        $this->receipt = $receipt;
        return $this;
    }

    public function setReference(Reference $reference): self {
        $this->reference = $reference;
        return $this;
    }
}
