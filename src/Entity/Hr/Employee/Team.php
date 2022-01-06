<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\TimeSlot;
use App\Entity\Management\Society\Company;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

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
                    'description' => 'Créer une équipe',
                    'summary' => 'Créer une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une équipe',
                    'summary' => 'Supprime une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une équipe',
                    'summary' => 'Récupère une équipe',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une équipe',
                    'summary' => 'Modifie une équipe',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:company', 'write:team', 'write:name', 'write:time-slot'],
            'openapi_definition_name' => 'Team-write'
        ],
        normalizationContext: [
            'groups' => ['read:company', 'read:id', 'read:name', 'read:time-slot', 'read:team'],
            'openapi_definition_name' => 'Team-read'
        ],
    ),
    ORM\Entity
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
        ApiProperty(description: 'Nom', required: true, example: 'Groupe 1'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/time-slots/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: TimeSlot::class),
        Serializer\Groups(['read:time-slot', 'write:time-slot'])
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
