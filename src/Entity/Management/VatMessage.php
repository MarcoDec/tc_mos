<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['message' => 'partial']),
    ApiResource(
        description: 'VatMessage',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les messages Vat',
                    'summary' => 'Récupère les messages Vat',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un message vat',
                    'summary' => 'Créer un message vat',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un message vat',
                    'summary' => 'Supprime un message vat',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un message vat',
                    'summary' => 'Modifie un message vat',
                ]
            ]
        ],
        shortName: 'VatMessage',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:vatMessage'],
            'openapi_definition_name' => 'VatMessage-write'
        ],
        normalizationContext: [
            'groups' => ['read:vatMessage', 'read:id'],
            'openapi_definition_name' => 'VatMessage-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'vat_message')
]
class VatMessage extends Entity {
    #[
        ApiProperty(description: 'Message', example: 'Ventes intra-communautaire : Exonération de TVA article 262 TERI du CGI..'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:vatMessage', 'write:vatMessage'])

    ]
    private ?string $message = null;

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(?string $message): void {
        $this->message = $message;
    }
}
