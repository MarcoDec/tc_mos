<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\DBAL\Types\Logistics\FamilyType;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\EntityId;
use App\Entity\Interfaces\CompanyInterface;
use App\Entity\Management\Society\Company\Company;
use App\Filter\SetFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: OrderFilter::class, properties: ['name']),
    ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial']),
    ApiFilter(filterClass: SetFilter::class, properties: ['families']),
    ApiResource(
        description: 'Entrepôt',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les entrepôts',
                    'summary' => 'Récupère les entrepôts'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un entrepôt',
                    'summary' => 'Créer un entrepôt'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un entrepôt',
                    'summary' => 'Supprime un entrepôt'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un entrepôt',
                    'summary' => 'Récupère un entrepôt'
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un entrepôt',
                    'summary' => 'Modifie un entrepôt'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:warehouse'],
            'openapi_definition_name' => 'Warehouse-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:warehouse'],
            'openapi_definition_name' => 'Warehouse-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity
]
class Warehouse extends EntityId implements CompanyInterface {
    #[
        ApiProperty(description: 'Compagnie', example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['write:warehouse'])
    ]
    private ?Company $company = null;

    /** @var string[] */
    #[
        ApiProperty(
            description: 'Familles',
            example: [FamilyType::TYPE_PRODUCTION],
            openapiContext: ['items' => ['enum' => FamilyType::TYPES, 'type' => 'string'], 'type' => 'array']
        ),
        ORM\Column(type: 'warehouse_families', nullable: true),
        Serializer\Groups(['read:warehouse', 'write:warehouse'])
    ]
    private array $families = [];

    #[
        ApiProperty(description: 'Nom', example: 'Magasin RIOZ'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:warehouse', 'write:warehouse'])
    ]
    private ?string $name = null;

    final public function addFamily(string $family): self {
        $this->families[] = $family;
        return $this;
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    /**
     * @return string[]
     */
    final public function getFamilies(): array {
        return $this->families;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function removeFamily(string $family): self {
        if (false !== $key = array_search($family, $this->families, true)) {
            /** @var int $key */
            array_splice($this->families, $key, 1);
        }
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }
}
