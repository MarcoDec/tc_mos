<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Hr\TimeSlot;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;

#[
    ApiResource(
        description: 'Equipe',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les équipes',
                    'summary' => 'Récupère les équipes',
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
class Team extends Entity {
    use CompanyTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/time-slots/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: TimeSlot::class),
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private ?TimeSlot $timeSlot;

    final public function getTimeSlot(): ?TimeSlot {
        return $this->timeSlot;
    }

    final public function setTimeSlot(?TimeSlot $timeSlot): self {
        $this->timeSlot = $timeSlot;
        return $this;
    }
}
