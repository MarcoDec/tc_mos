<?php
namespace App\Entity\Production\Manufacturing;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiProperty;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['employee.id' => 'partial', 'operation.embState.state' => 'partial', 'operation.embBlocker.state' => 'partial', 'operation.order.ref' => 'partial', 'operation.workstation.name' => 'partial', 'operation.startedDate' => 'partial', 'operation.duration' => 'partial', 'operation.actualQuantity.code' => 'partial', 'operation.actualQuantity.value' => 'partial', 'operation.quantityProduced.code' => 'partial', 'operation.quantityProduced.value', 'operation.quantityProduced.value' => 'partial', 'operation.operation.cadence.value' => 'partial', 'operation.operation.cadence.code' => 'partial', 'operation.embState.state' => 'partial', 'operation.embBlocker.state' => 'partial']),
    ApiFilter(filterClass: DateFilter::class, properties: ['operation.startedDate' => '>']),
    ApiResource(
        collectionOperations: [
            'get' =>[
                'normalization_context' => [
                    'groups' => ['read:id', 'read:operation-employee:collection'],
                    'openapi_definition_name' => 'OperationEmployee-collection',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les operations d\'employé',
                    'summary' => 'Récupère les operations d\'employé'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:operation-employee'],
                    'openapi_definition_name' => 'OperationEmployee-create'
                ],
                'openapi_context' => [
                    'description' => 'Créer une opération d\'employé',
                    'summary' => 'Créer une opération d\'employé'
                ],
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une opération d\'employé',
                    'summary' => 'Supprime une opération d\'employé'
                ],
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une opération d\'employé',
                    'summary' => 'Récupère une opération d\'employé'
                ]
            ],

        ],
        shortName: 'OperationEmployee',
            denormalizationContext: [
            'groups' => ['write:operation-employee'],
            'openapi_definition_name' => 'OperationEmployees-write'
        ],
        normalizationContext: [
            'groups' => ['read:operation-employee'],
           'openapi_definition_name' => 'OperationEmployee-read',
           'skip_null_values' => false
       ],
        paginationClientEnabled: true
    ),
    ORM\Entity,
    ORM\Table(name: 'operation_employee')

]
class OperationEmployee extends Entity
{
    #[  
        ApiProperty(description: 'Employée', readableLink: true),
        ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'operationEmployees'),
        Serializer\Groups(['read:operation-employee:collection'])
    ]
    private ?Employee $employee = null;
    #[
        ApiProperty(description: 'Opération', readableLink: true),
        ORM\ManyToOne(targetEntity: Operation::class, inversedBy: 'operationEmployees'),
        Serializer\Groups(['read:operation-employee:collection'])
    ]
    private ?Operation $operation = null;

    final public function setEmployee(?Employee $employee): self {
        $this->employee = $employee;
        return $this;
    }
    final public function getEmployee(): ?Employee {
        return $this->employee;
    }
    final public function setOperation(?Operation $operation): self {
        $this->operation = $operation;
        return $this;
    }
    final public function getOperation(): ?Operation {
        return $this->operation;
    }
}