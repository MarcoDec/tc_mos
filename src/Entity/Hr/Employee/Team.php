<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\TimeSlot;
use App\Entity\Management\Society\Company;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Équipe',
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
            'groups' => ['write:team'],
            'openapi_definition_name' => 'Team-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:team'],
            'openapi_definition_name' => 'Team-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Team extends Entity {
    #[
        ApiProperty(description: 'Company', readableLink: false, example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private ?Company $company = null;

    #[
        ApiProperty(description: 'Nom', example: 'Groupe 1'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Compagnie dirigeante', readableLink: false, example: '/api/time-slots/2'),
        ORM\ManyToOne,
        Serializer\Groups(['read:team', 'write:team'])
    ]
    private ?TimeSlot $timeSlot = null;

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getTimeSlot(): ?TimeSlot {
        return $this->timeSlot;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setTimeSlot(?TimeSlot $timeSlot): self {
        $this->timeSlot = $timeSlot;
        return $this;
    }
}
