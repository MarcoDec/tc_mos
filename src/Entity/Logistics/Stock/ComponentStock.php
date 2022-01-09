<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Stock des composants',
        collectionOperations: [
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un stock de composants',
                    'summary' => 'Créer un stock de composants',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: [
        ],
        shortName: 'ComponentStock',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:stock', 'write:warehouse', 'write:measure', 'write:name', 'write:unit', 'write:company'],
            'openapi_definition_name' => 'ComponentStock-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:stock', 'read:warehouse', 'read:measure', 'read:name', 'read:unit', 'read:company'],
            'openapi_definition_name' => 'ComponentStock-read'
        ],
    ),
    ORM\Entity
]
class ComponentStock extends Stock {
    #[
        ApiProperty(description: 'Composant', required: false, readableLink: false, example: '/api/components/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Component::class),
        Serializer\Groups(['read:stock', 'write:stock'])
    ]
    private ?Component $item;

    final public function getItem(): ?Component {
        return $this->item;
    }

    final public function getItemType(): string {
        return 'Component';
    }

    final public function setItem(?Component $item): self {
        $this->item = $item;

        return $this;
    }
}
