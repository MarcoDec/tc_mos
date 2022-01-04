<?php

namespace App\Entity\Logistics;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Entrepôts',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les entrepôts',
                    'summary' => 'Récupère les entrepôts',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un entrepôt',
                    'summary' => 'Créer un entrepôt',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un entrepôt',
                    'summary' => 'Supprime un entrepôt',
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
                    'summary' => 'Modifie un entrepôt',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:name', 'write:company', 'write:warehouse'],
            'openapi_definition_name' => 'Warehouse-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:name', 'read:company', 'read:warehouse'],
            'openapi_definition_name' => 'Warehouse-read'
        ],
    ),
    ORM\Entity
]
class Warehouse extends Entity {
    use CompanyTrait;
    use NameTrait;

    #[
        ApiProperty(description: 'Company', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Entrepôt Victor Hugo'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /** @var mixed[] */
    #[
        ApiProperty(description: 'Familles', required: false),
        ORM\Column(nullable: true, type: 'warehouse_families'),
        Serializer\Groups(['read:warehouse', 'write:warehouse'])
    ]
    private array $families = [];

    final public function addFamily(string $family): self {
        if (!in_array($family, $this->families)) {
            $this->families[] = $family;
            sort($this->families);
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    final public function getFamilies(): array {
        return $this->families;
    }

    final public function removeFamily(string $family): self {
        if (false !== $key = array_search($family, $this->families, true)) {
            /** @phpstan-ignore-next-line */
            array_splice($this->families, $key, 1);
        }

        return $this;
    }
}
