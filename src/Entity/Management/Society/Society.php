<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Address;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Traits\SocietyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(OrderFilter::class, properties: ['name']),
    ApiResource(
        description: 'Société',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les sociétés',
                    'summary' => 'Récupère les sociétés',
                ],
                'normalization_context' => [
                    'groups' => 'read:society:collection'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une société',
                    'summary' => 'Créer une société',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une société',
                    'summary' => 'Supprime une société',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une société',
                    'summary' => 'Récupère une société'
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une société',
                    'summary' => 'Modifie une société',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:society', 'write:name', 'write:address', 'write:incoterms', 'write:copper'],
            'openapi_definition_name' => 'Society-write'
        ],
        normalizationContext: [
            'groups' => ['read:society', 'read:id', 'read:name', 'read:address', 'read:incoterms', 'read:invoice-time-due', 'read:copper'],
            'openapi_definition_name' => 'Society-read'
        ],
    ),
    ORM\Entity
]
class Society extends Entity {
    use SocietyTrait {
        __construct as private societyConstruct;
    }

    #[
        ApiProperty(description: 'Adresse'),
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Détails bancaires', required: false, example: 'IBAN/RIB/Nom de banque'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $bankDetails = null;

    #[
        ApiProperty(description: 'Cuivre'),
        ORM\Embedded(Copper::class),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Copper $copper;

    #[
        ApiProperty(description: 'Numéro de fax', required: false, example: '02 17 21 11 11'),
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $fax = null;

    #[
        ApiProperty(description: 'Forme juridique', required: false, example: 'SARL'),
        ORM\Column(type: 'string', length: 50, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $legalForm = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'TConcept'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'read:society:collection'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Notes libres sur la société'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'SIREN', required: false, example: '123 456 789'),
        ORM\Column(type: 'string', length: 50, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $siren = null;

    #[
        ApiProperty(description: 'Site internet', required: false, example: 'https://www.societe.fr'),
        Assert\Url,
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?string $web = null;

    public function __construct() {
        $this->societyConstruct();
        $this->address = new Address();
        $this->copper = new Copper();
    }

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getBankDetails(): ?string {
        return $this->bankDetails;
    }

    final public function getCopper(): Copper {
        return $this->copper;
    }

    final public function getFax(): ?string {
        return $this->fax;
    }

    final public function getLegalForm(): ?string {
        return $this->legalForm;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getSiren(): ?string {
        return $this->siren;
    }

    final public function getWeb(): ?string {
        return $this->web;
    }

    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setBankDetails(?string $bankDetails): self {
        $this->bankDetails = $bankDetails;
        return $this;
    }

    final public function setCopper(Copper $copper): self {
        $this->copper = $copper;
        return $this;
    }

    final public function setFax(?string $fax): self {
        $this->fax = $fax;
        return $this;
    }

    final public function setLegalForm(?string $legalForm): self {
        $this->legalForm = $legalForm;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setSiren(?string $siren): self {
        $this->siren = $siren;
        return $this;
    }

    final public function setWeb(?string $web): self {
        $this->web = $web;
        return $this;
    }
}
