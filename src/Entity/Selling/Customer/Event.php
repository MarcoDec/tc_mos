<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'customer' => ['name', 'required' => true]
    ]),
    ApiResource(
        description: 'Evénement',
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
                'security' => 'is_granted(\''.Roles::ROLE_SELLING_WRITER.'\')',
                'denormalization_context' => [
                    'groups' => ['write:name', 'write:event_date', 'write:customer-event']
                ]
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
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un événement',
                    'summary' => 'Récupère un événement',
                ]
            ],
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
            'groups' => ['write:name', 'write:event_date', 'write:customer-event', 'write:event', 'write:company'],
            'openapi_definition_name' => 'CustomerEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:customer-event', 'read:name', 'read:event_date', 'read:company'],
            'openapi_definition_name' => 'CustomerEvent-read'
        ],
    ),
    ORM\Entity
]
class Event extends AbstractEvent {
    public const EVENT_HOLIDAY = 'holiday';
    public const EVENT_KINDS = [self::EVENT_HOLIDAY];

    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class, inversedBy: 'events'),
        Serializer\Groups(['read:customer-event', 'write:customer-event'])
    ]
    private Customer $customer;

    #[
        ApiProperty(description: 'Type', example: self::EVENT_HOLIDAY),
        Assert\Choice(choices: self::EVENT_KINDS),
        ORM\Column(options: ['default' => self::EVENT_HOLIDAY], type: 'string'),
        Serializer\Groups(['read:customer-event', 'write:customer-event'])
    ]
    private string $kind = self::EVENT_HOLIDAY;

    public function getCustomer(): Customer {
        return $this->customer;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    public function setCustomer(Customer $customer): void {
        $this->customer = $customer;
    }

    final public function setKind(string $kind): self {
        $this->kind = $kind;

        return $this;
    }
}
