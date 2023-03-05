<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Événement sur un client',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les événements',
                    'summary' => 'Récupère les événements',
                ],
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un événement',
                    'summary' => 'Créer un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un événement',
                    'summary' => 'Supprime un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_ADMIN.'\')'
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un événement',
                    'summary' => 'Modifie un événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')'
            ],
        ],
        shortName: 'CustomerEvent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_SELLING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:event'],
            'openapi_definition_name' => 'CustomerEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:id'],
            'openapi_definition_name' => 'CustomerEvent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table('customer_event')
]
class Event extends AbstractEvent {
    final public const EVENT_HOLIDAY = 'holiday';
    final public const EVENT_KINDS = [self::EVENT_HOLIDAY];

    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private Customer $customer;

    #[
        ApiProperty(description: 'Type', example: self::EVENT_HOLIDAY),
        Assert\Choice(choices: self::EVENT_KINDS),
        ORM\Column(options: ['default' => self::EVENT_HOLIDAY]),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private string $kind = self::EVENT_HOLIDAY;

    final public function getCustomer(): Customer {
        return $this->customer;
    }

    final public function getKind(): string {
        return $this->kind;
    }

    final public function setCustomer(Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setKind(string $kind): self {
        $this->kind = $kind;
        return $this;
    }
}
