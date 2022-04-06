<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Serializer\Annotation as Serializer;

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
                ],
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:currency'],
            'openapi_definition_name' => 'Currency-write'
        ],
        normalizationContext: [
            'groups' => ['read:currency', 'read:id'],
            'openapi_definition_name' => 'Currency-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(repositoryClass: CurrencyRepository::class)
]
class Currency extends AbstractUnit {
    #[
        ApiProperty(description: 'Enfants ', readableLink: false, example: ['/api/currencies/2', '/api/currencies/3']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class),
        Serializer\Groups(['read:currency'])
    ]
    protected Collection $children;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'EUR'),
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected ?string $code = null;

    protected ?string $name = null;

    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/currencies/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['read:currency'])
    ]
    protected $parent;

    #[
        ApiProperty(description: 'Active', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private bool $active = false;

    #[
        ApiProperty(description: 'Nom', example: 'Euro'),
        Serializer\Groups(['read:currency'])
    ]
    final public function getName(): ?string {
        return !empty($this->getCode()) ? Currencies::getName($this->getCode()) : null;
    }

    #[
        ApiProperty(description: 'Symbole', example: '€'),
        Serializer\Groups(['read:currency'])
    ]
    final public function getSymbol(): ?string {
        return !empty($this->getCode()) ? Currencies::getSymbol($this->getCode()) : null;
    }

    final public function isActive(): bool {
        return $this->active;
    }

    final public function setActive(bool $active): self {
        $this->active = $active;
        return $this;
    }
}
