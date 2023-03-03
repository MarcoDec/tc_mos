<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\CountryProvider;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    description: 'Pays',
    operations: [new GetCollection(
        uriTemplate: '/countries/options',
        openapiContext: ['description' => 'Récupère les pays', 'summary' => 'Récupère les pays'],
        provider: CountryProvider::class
    )],
    outputFormats: 'jsonld',
    normalizationContext: ['groups' => ['country'], 'skip_null_values' => false],
    paginationEnabled: false
)]
class Country {
    /** @var string[] */
    private const MAIN = ['CH', 'FR', 'MD', 'RU', 'TN', 'US'];

    public function __construct(private readonly string $code) {
    }

    #[ApiProperty(required: true, example: 'FR'), Serializer\Groups('country')]
    public function getCode(): string {
        return $this->code;
    }

    #[
        ApiProperty(required: true, example: 'Principaux', openapiContext: ['enum' => ['Principaux', 'Autres']]),
        Serializer\Groups('country')
    ]
    public function getGroup(): string {
        return in_array($this->code, self::MAIN, true) ? 'Principaux' : 'Autres';
    }

    #[ApiProperty(required: true, identifier: true, example: 'FR'), Serializer\Groups('country')]
    public function getId(): string {
        return $this->code;
    }

    #[ApiProperty(required: true, example: 'France'), Serializer\Groups('country')]
    public function getName(): string {
        return Countries::getName($this->code);
    }

    #[ApiProperty(required: true, example: 'FR'), Serializer\Groups('country')]
    public function getText(): string {
        return $this->code;
    }
}
