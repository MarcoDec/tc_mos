<?php

namespace App\Api\Resource\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    description: 'Statut employé',
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
        'groups' => ['read:employee-state'],
        'openapi_definition_name' => 'EmployeeState-read',
        'skip_null_values' => false
    ],
    paginationEnabled: false
)]
final class EmployeeState {
    public function __construct(private readonly string $id, private readonly string $text) {
    }

    #[ApiProperty(identifier: true), Serializer\Groups(['read:employee-state'])]
    public function getId(): string {
        return $this->id;
    }

    #[Serializer\Groups(['read:employee-state'])]
    public function getText(): string {
        return $this->text;
    }
}
