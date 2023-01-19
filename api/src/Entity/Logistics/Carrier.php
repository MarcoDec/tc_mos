<?php

declare(strict_types=1);

namespace App\Entity\Logistics;

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
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: OrderFilter::class, id: 'address-sorter', properties: Address::sorter),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(filterClass: SearchFilter::class, id: 'address', properties: Address::filter),
    ApiResource(
        description: 'Transporteur',
        operations: [
            new GetCollection(
                openapiContext: ['description' => 'Récupère les transporteurs', 'summary' => 'Récupère les transporteurs'],
                security: Role::GRANTED_LOGISTICS_READER
            ),
            new Post(
                openapiContext: ['description' => 'Créer un transporteur', 'summary' => 'Créer un transporteur'],
                security: Role::GRANTED_LOGISTICS_WRITER,
                processor: PersistProcessor::class
            ),
            new Delete(
                openapiContext: ['description' => 'Supprime un transporteur', 'summary' => 'Supprime un transporteur'],
                security: Role::GRANTED_LOGISTICS_ADMIN,
                processor: RemoveProcessor::class
            ),
            new Patch(
                inputFormats: ['json' => ['application/merge-patch+json']],
                openapiContext: ['description' => 'Modifie un transporteur', 'summary' => 'Modifie un transporteur'],
                security: Role::GRANTED_LOGISTICS_WRITER,
                processor: PersistProcessor::class
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['address', 'id', 'carrier-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'carrier-read'
        ],
        denormalizationContext: ['groups' => ['address', 'carrier-write']],
        order: ['name' => 'asc']
    ),
    ORM\Entity
]
class Carrier extends Entity {
    #[
        ApiProperty(description: 'Adresse', genId: false),
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['carrier-read', 'carrier-write'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Nom', example: 'DHL'),
        Assert\Length(min: 3, max: 50),
        Assert\NotBlank,
        ORM\Column(length: 50),
        Serializer\Groups(['carrier-read', 'carrier-write'])
    ]
    private ?string $name = null;

    public function __construct() {
        $this->address = new Address();
    }

    public function getAddress(): Address {
        return $this->address;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
