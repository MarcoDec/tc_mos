<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Devises',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les devises',
                    'summary' => 'Récupère les devises',
                ]
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une devise',
                    'summary' => 'Modifie une devise',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:currency', 'write:currency'],
            'openapi_definition_name' => 'Currency-write'
        ],
        normalizationContext: [
            'groups' => ['read:currency', 'read:id'],
            'openapi_definition_name' => 'Currency-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(repositoryClass: CurrencyRepository::class),
    ORM\Table
]
class Currency extends Entity {
    #[
        ApiProperty(description: 'Active', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private bool $active = false;

    #[
        ApiProperty(description: 'Code', required: true, example: 'EUR'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:currency'])
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Taux (€)', required: true, example: 1),
        Assert\NotBlank,
        Assert\Positive,
        ORM\Column(options: ['default' => 1, 'unsigned' => true]),
        Serializer\Groups(['read:currency'])
    ]
    private float $rate = 1;

    final public function getCode(): ?string {
        return $this->code;
    }

    #[
        ApiProperty(description: 'Nom', example: 'Euro'),
        Serializer\Groups(['read:currency'])
    ]
    final public function getName(): ?string {
        return !empty($this->code) ? Currencies::getName($this->code) : null;
    }

    final public function getRate(): float {
        return $this->rate;
    }

    #[
        ApiProperty(description: 'Symbole', example: '€'),
        Serializer\Groups(['read:currency'])
    ]
    final public function getSymbol(): ?string {
        return !empty($this->code) ? Currencies::getSymbol($this->code) : null;
    }

    final public function isActive(): bool {
        return $this->active;
    }

    final public function setActive(bool $active): self {
        $this->active = $active;
        return $this;
    }

    final public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    final public function setRate(float $rate): self {
        $this->rate = $rate;
        return $this;
    }
}
