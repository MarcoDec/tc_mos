<?php

namespace App\Api\Resource;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    description: 'Pays',
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'description' => 'Récupère les pays',
                'summary' => 'Récupère les pays',
            ]
        ]
    ],
    itemOperations: ['get' => NO_ITEM_GET_OPERATION],
    normalizationContext: [
        'groups' => ['read:country'],
        'openapi_definition_name' => 'Country-read',
        'skip_null_values' => false
    ],
    paginationEnabled: false
)]
final class Country {
    private const MAIN = ['CH', 'FR', 'MD', 'RU', 'TN', 'US'];

    public function __construct(private readonly string $code) {
    }

    #[
        ApiProperty(identifier: true),
        Serializer\Groups(['read:country'])
    ]
    public function getCode(): string {
        return $this->code;
    }

    #[Serializer\Groups(['read:country'])]
    public function getGroup(): string {
        return in_array($this->code, self::MAIN) ? 'Principaux' : 'Autres';
    }

    #[Serializer\Groups(['read:country'])]
    public function getId(): string {
        return $this->code;
    }

    #[Serializer\Groups(['read:country'])]
    public function getName(): string {
        return Countries::getName($this->code);
    }
}
