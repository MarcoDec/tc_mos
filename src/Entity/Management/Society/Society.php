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
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SocietyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(OrderFilter::class, properties: [
        'name',
    ]),
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
    use NameTrait;
    use SocietyTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'TConcept'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'read:society:collection'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Compte de comptabilité', required: false, example: 'D554DZ5'),
        Assert\Length(max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $accountingAccount;

    #[
        ApiProperty(description: 'Adresse'),
        ORM\Embedded,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Détails bancaires', required: false, example: 'IBAN/RIB/Nom de banque'),
        ORM\Column(type: 'text'),
        Serializer\Groups(['read:society'])
    ]
    private ?string $bankDetails;

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
    private ?string $fax;

    #[
        ApiProperty(description: 'Incoterms', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Incoterms::class),
        Serializer\Groups(['read:incoterms', 'write:incoterms'])
    ]
    private ?Incoterms $incoterms;

    #[
        ApiProperty(description: 'Délai de paiement des facture', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: InvoiceTimeDue::class),
        Serializer\Groups(['read:invoice-time-due', 'write:invoice-time-due'])
    ]
    private ?InvoiceTimeDue $invoiceTimeDue;

    #[
        ApiProperty(description: 'Forme juridique', required: false, example: 'SARL'),
        ORM\Column(type: 'string', length: 50, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $legalForm;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Notes libres sur la société'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $notes;

    #[
        ApiProperty(description: 'Taux ppm', required: false, example: '10'),
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?int $ppmRate = 10;

    #[
        ApiProperty(description: 'SIREN', required: false, example: '123 456 789'),
        ORM\Column(type: 'string', length: 50, nullable: true),
        Serializer\Groups(['read:society'])
    ]
    private ?string $siren;

    #[
        ApiProperty(description: 'TVA', required: false, example: 'FR'),
        Assert\Length(max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?string $vat = null;

    #[
        ApiProperty(description: 'Site internet', required: false, example: 'https://www.societe.fr'),
        Assert\Url,
        ORM\Column(type: 'string', length: 255, nullable: true),
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private ?string $web;

    public function __construct() {
        $this->address = new Address();
        $this->copper = new Copper();
    }

    public function getAddress(): Address {
        return $this->address;
    }

    final public function getBankDetails(): ?string {
        return $this->bankDetails;
    }

    public function getCopper(): Copper {
        return $this->copper;
    }

    final public function getFax(): ?string {
        return $this->fax;
    }

    final public function getLegalForm(): ?string {
        return $this->legalForm;
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

    public function setAddress(Address $address): self {
        $this->address = $address;

        return $this;
    }

    final public function setBankDetails(string $bankDetails): self {
        $this->bankDetails = $bankDetails;

        return $this;
    }

    public function setCopper(Copper $copper): self {
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
