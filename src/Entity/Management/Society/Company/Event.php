<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use App\Entity\Management\Society\Company;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: [
        'company' => ['name', 'required' => true]
    ]),
    ApiResource(
        description: 'Evénement',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les événements',
                    'summary' => 'Récupère les événements',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer événement',
                    'summary' => 'Créer événement',
                ],
                'denormalization_context' => [
                    'groups' => ['write:name', 'write:company-event:post', 'write:event']
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime événement',
                    'summary' => 'Supprime événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie événement',
                    'summary' => 'Modifie événement',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        shortName: 'CompanyEvent',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:company', 'write:event', ' write:company-event', 'write:name', 'write:event_date', 'write:company-event'],
            'openapi_definition_name' => 'CompanyEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:id', 'read:company-event', 'read:company', 'read:name', ' write:company-event', 'read:event_date'],
            'openapi_definition_name' => 'CompanyEvent-read'
        ],
    ),
    ORM\Entity,
    ORM\Table('company_event')
]
class Event extends AbstractEvent {
    public const EVENT_HOLIDAY = 'holiday';
    public const EVENT_KINDS = [self::EVENT_HOLIDAY];

    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class, inversedBy: 'events'),
        Serializer\Groups(['read:company', 'read:company-event', 'write:company-event:post', 'write:company-event'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'Type', example: self::EVENT_HOLIDAY),
        Assert\Choice(choices: self::EVENT_KINDS),
        ORM\Column(options: ['default' => self::EVENT_HOLIDAY], type: 'string'),
        Serializer\Groups(['read:company-event', 'write:company-event', 'write:company-event:post', 'write:company-event'])
    ]
    private string $kind = self::EVENT_HOLIDAY;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;

        return $this;
    }

    final public function setKind(string $kind): self {
        $this->kind = $kind;

        return $this;
    }
}
