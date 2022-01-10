<?php

namespace App\Entity\It;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Embeddable\It\CurrentPlace;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Embeddable\Hr\Employee\Roles;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Demande',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les demandes',
                    'summary' => 'Récupère les demandes',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une demande',
                    'summary' => 'Créer une demande'
                ],
                'denormalization_context' => [
                    'groups' => ['write:name', 'write:post']
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une demande',
                    'summary' => 'Supprime une demande',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une demande',
                    'summary' => 'Modifie une demande',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_IT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:request', 'write:employee', 'write:current_place'],
            'openapi_definition_name' => 'Request-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:request', 'read:employee', 'read:current_place'],
            'openapi_definition_name' => 'Request-read'
        ],
    ),
    ORM\Entity
]
class Request extends Entity {
    use NameTrait;

    public const WF_PLACE_ACCEPTED = 'accepted';
    public const WF_PLACE_ASKED = 'asked';
    public const WF_PLACE_CLOSED = 'closed';
    public const WF_PLACE_REFUSED = 'refused';
    public const WF_TR_ACCEPT = 'accept';
    public const WF_TR_CLOSE = 'close';
    public const WF_TR_REFUSE = 'refuse';

    #[
        ApiProperty(description: 'Nom', required: true),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Date de demande', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:request', 'write:request'])
    ]
    private ?\DatetimeInterface $askedAt = null;

    #[
        ApiProperty(description: 'Employé demandeur', required: false, readableLink: false, example: '/api/employees/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Employee::class),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?Employee $askedBy = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'locked'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:current_place', 'write:current_place'])
    ]
    protected CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Delai', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:request', 'write:request'])
    ]
    private ?\DatetimeInterface $delay = null;

    #[
        ApiProperty(description: 'Description', required: false, example: 'Mise à jour'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:request', 'write:request', 'write:post'])
    ]
    private ?string $description = null;
    
    #[
        ApiProperty(description: 'Version', required: false, example: '16.3.6'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:request', 'write:request'])
    ]
    private ?string $version = null;

    public function __construct() {
        $this->currentPlace = new CurrentPlace();
    }

    final public function getAskedAt(): ?\DateTimeInterface {
        return $this->askedAt;
    }

    final public function getAskedBy(): ?Employee {
        return $this->askedBy;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getDelay(): ?\DateTimeInterface {
        return $this->delay;
    }

    final public function getDescription(): ?string {
        return $this->description;
    }

    final public function getTrafficLight(): int {
        return $this->currentPlace->getTrafficLight();
    }

    final public function getVersion(): ?string {
        return $this->version;
    }

    final public function setAskedAt(?\DateTimeInterface $askedAt): self {
        $this->askedAt = $askedAt;
        return $this;
    }

    final public function setAskedBy(?Employee $askedBy): self {
        $this->askedBy = $askedBy;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setDelay(?\DateTimeInterface $delay): self {
        $this->delay = $delay;
        return $this;
    }

    final public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    final public function setVersion(?string $version): self {
        $this->version = $version;
        return $this;
    }
}
