<?php

namespace App\Api\Resource\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    description: 'Statut d\'un employé',
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'description' => 'Récupère les statuts',
                'summary' => 'Récupère les statuts',
            ],
            'path' => '/employee-states/options'
        ]
    ],
    itemOperations: ['get' => NO_ITEM_GET_OPERATION],
    normalizationContext: [
        'groups' => ['read:option'],
        'openapi_definition_name' => 'EmployeeState-read',
        'skip_null_values' => false
    ],
    paginationEnabled: false
)]
final class EmployeeState {
    public function __construct(private readonly string $text, private readonly string $value) {
    }

    #[Serializer\Groups(['read:option'])]
    public function getText(): string {
        return $this->text;
    }

    #[ApiProperty(identifier: true), Serializer\Groups(['read:option'])]
    public function getValue(): string {
        return $this->value;
    }
}
