<?php

namespace App\Entity\Logistics\Stock;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use Symfony\Component\String\UnicodeString;

/**
 * @phpstan-type StockGroupItem array{"@id": string, "@type": string, id: int, code: string, name: string, unitCode: string}
 * @phpstan-type StockGroupQuantity array{code: string, value: float}
 */
#[ApiResource(
    description: 'Groupe de stocks',
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'description' => 'Récupère les stocks groupés par référence et lot',
                'parameters' => [
                    ['in' => 'query', 'name' => 'location', 'schema' => ['type' => 'string']],
                    ['in' => 'query', 'name' => 'warehouse', 'schema' => ['type' => 'string'], 'required' => true]
                ],
                'summary' => 'Récupère les stocks groupés par référence et lot',
                'tags' => ['Stock']
            ]
        ]
    ],
    itemOperations: ['get' => NO_ITEM_GET_OPERATION],
    shortName: 'StockGroup',
    attributes: [
        'security' => 'is_granted(\''.Roles::ROLE_LOGISTICS_READER.'\')'
    ],
   paginationClientEnabled: true
)]
final class Group {
    private readonly string $id;

    /**
     * @param StockGroupItem     $item
     * @param StockGroupQuantity $quantity
     */
    public function __construct(
        private readonly ?string $batchNumber,
        string $id,
        private readonly array $item,
        private readonly ?string $location,
        private readonly array $quantity,
        private readonly int $warehouse,
    ) {
        $this->id = (new UnicodeString($id))->replaceMatches('/[^a-zA-Z0-9\-]+/', '')->toString();
    }

    #[ApiProperty(description: 'Numéro de lot', example: '20221031')]
    public function getBatchNumber(): ?string {
        return $this->batchNumber;
    }

    #[ApiProperty(identifier: true, example: '1')]
    public function getId(): string {
        return $this->id;
    }

    /** @return StockGroupItem */
    #[ApiProperty(
        description: 'Élément',
        openapiContext: [
            'properties' => [
                '@id' => ['example' => '/api/components/1', 'type' => 'string'],
                '@type' => ['example' => 'Component', 'type' => 'string'],
                'id' => ['example' => 1, 'type' => 'number'],
                'code' => ['example' => 'FIX-1', 'type' => 'string'],
                'name' => ['example' => '2702 SCOTCH ADHESIF PVC T2 19MMX33M NOIR', 'type' => 'string'],
                'unitCode' => ['example' => 'U', 'type' => 'string']
            ],
            'type' => 'object'
        ]
    )]
    public function getItem(): array {
        return $this->item;
    }

    #[ApiProperty(description: 'Emplacement', example: 'P01')]
    public function getLocation(): ?string {
        return $this->location;
    }

    /** @return StockGroupQuantity */
    #[ApiProperty(description: 'Quantité', openapiContext: ['$ref' => '#/components/schemas/Measure-unitary'])]
    public function getQuantity(): array {
        return $this->quantity;
    }

    #[ApiProperty(description: 'Entrepôt', example: '/api/warehouses/1')]
    public function getWarehouse(): int {
        return $this->warehouse;
    }
}
