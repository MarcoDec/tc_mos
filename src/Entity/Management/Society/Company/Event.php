<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Event as AbstractEvent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Événement',
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
            'get' => NO_ITEM_GET_OPERATION,
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
            'groups' => ['write:event'],
            'openapi_definition_name' => 'CompanyEvent-write'
        ],
        normalizationContext: [
            'groups' => ['read:event', 'read:id'],
            'openapi_definition_name' => 'CompanyEvent-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity,
    ORM\Table('company_event')
]
class Event extends AbstractEvent {
    public const EVENT_HOLIDAY = 'holiday';
    public const EVENT_KINDS = [self::EVENT_HOLIDAY];

    #[
        ApiProperty(description: 'Compagnie', readableLink: false, example: '/api/companies/1'),
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne,
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Type', example: self::EVENT_HOLIDAY),
        Assert\Choice(choices: self::EVENT_KINDS),
        ORM\Column(options: ['default' => self::EVENT_HOLIDAY]),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private string $kind = self::EVENT_HOLIDAY;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getKind(): string {
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
