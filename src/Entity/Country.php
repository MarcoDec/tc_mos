<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

//#[ApiResource(
//    description: 'Pays',
//    collectionOperations: [
//        'get' => [
//            'path' => '/countries/options',
//            'openapi_context' => [
//                'description' => 'Récupère les pays',
//                'summary' => 'Récupère les pays'
//            ]
//        ]
//    ],
//    itemOperations: ['get' => NO_ITEM_GET_OPERATION],
//    paginationEnabled: false
//)]
final class Country {
    #[ApiProperty(description: 'Code', identifier: true, example: 'FR')]
    private readonly string $code;

    #[ApiProperty(description: 'Nom', example: 'France')]
    private readonly string $name;

    public function __construct(string $code, string $name) {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getName(): string {
        return $this->name;
    }
}
