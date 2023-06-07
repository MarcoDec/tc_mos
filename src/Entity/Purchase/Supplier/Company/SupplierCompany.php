<?php

namespace App\Entity\Purchase\Supplier\Company;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Purchase\Supplier\Supplier;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['company'=>'exact', 'supplier'=>'exact']),
    ApiResource(
        description: 'SupplierCompany',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'summary' => 'Récupère les Relations SupplierCompany',
                    'description' => 'Récupère les relations SupplierCompany, soit le tableau listant les compagnies qui gère chacun des fournisseurs'
                    ]
            ],
            'post' => [
                'openapi_context' => [
                    'summary' => 'Crée une Relation SupplierCompany',
                    'description' => 'Crée une relation SupplierCompany, et indique donc qu\'une compagnie gère désormais le fournisseur passé en paramètre'
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'summary' => 'Supprime une Relation SupplierCompany',
                    'description' => 'Supprime une relation SupplierCompany, et indique donc qu\'une compagnie ne gère désormais plus le fournisseur passé en paramètre'
                ]
            ],
            'get'
        ],
        denormalizationContext: [
           'groups' => ['write:supplier-company'],
           'openapi_definition_name' => 'SupplierCompany-write'
        ],
        normalizationContext: [
            'groups' => ['read:supplier-company', 'read:id'],
            'openapi_definition_name' => "SupplierCompany-read"
        ]
    ),
    ORM\Entity
]
class SupplierCompany extends Entity
{
    #[
        ApiProperty(description: 'Fournisseur', example: '/api/suppliers/1'),
        ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'supplierCompanies'),
        Groups(['write:supplier-company', 'read:supplier-company'])
    ]
    private Supplier $supplier;

    #[
        ApiProperty(description: 'Companie gérant (interagissant avec) le fournisseur', example: '/api/companies/1'),
        ORM\ManyToOne(targetEntity: Company::class),
        Groups(['write:supplier-company', 'read:supplier-company'])
    ]
    private Company $company;

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(Supplier $supplier): SupplierCompany
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): SupplierCompany
    {
        $this->company = $company;
        return $this;
    }

}
