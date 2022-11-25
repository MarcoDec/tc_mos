<?php

declare(strict_types=1);

namespace App\Entity\Hr;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use App\Filter\OrderFilter;
use App\Filter\SearchFilter;
use App\State\PersistProcessor;
use App\State\RemoveProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name', 'surname']),
    ApiFilter(filterClass: OrderFilter::class, id: 'address-sorter', properties: Address::sorter),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial', 'surname' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
    ApiResource(
        description: 'Formateur extérieur',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les formateurs', 'summary' => 'Récupère les formateurs'],
                security: Role::GRANTED_HR_READER
            ),
            new Post(
                openapiContext: ['description' => 'Créer un formateur', 'summary' => 'Créer un formateur'],
                security: Role::GRANTED_HR_WRITER,
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un formateur', 'summary' => 'Supprime un formateur'],
                security: Role::GRANTED_HR_ADMIN,
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un formateur', 'summary' => 'Modifie un formateur'],
                security: Role::GRANTED_HR_WRITER,
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['address', 'id', 'out-trainer-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'out-trainer-read'
        ],
        denormalizationContext: ['groups' => ['address', 'out-trainer-write']],
        order: ['surname' => 'asc']
    ),
    ORM\Entity
]
class OutTrainer extends Entity {
    #[
        ApiProperty(description: 'Adresse', genId: false),
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['out-trainer-read', 'out-trainer-write'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Prénom', example: 'Rawaa'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['out-trainer-read', 'out-trainer-write'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nom', example: 'CHRAIET'),
        Assert\Length(min: 3, max: 30),
        Assert\NotBlank,
        ORM\Column(length: 30),
        Serializer\Groups(['out-trainer-read', 'out-trainer-write'])
    ]
    private ?string $surname = null;

    public function __construct() {
        $this->address = new Address();
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name === null ? $name : mb_convert_case($name, MB_CASE_TITLE);
        return $this;
    }

    public function setSurname(?string $surname): self {
        $this->surname = $surname === null ? $surname : mb_strtoupper($surname);
        return $this;
    }
}
