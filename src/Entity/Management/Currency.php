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
            'groups' => ['Currency-write'],
            'openapi_definition_name' => 'Currency-write'
        ],
        normalizationContext: [
            'groups' => ['Currency-read', 'Entity:id'],
            'openapi_definition_name' => 'Currency-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(repositoryClass: CurrencyRepository::class),
    ORM\Table
]
class Currency extends AbstractUnit {
    /** @var Collection<int, Unit> */
    #[
        ApiProperty(description: 'Enfants ', readableLink: false, example: ['/api/currencies/2', '/api/currencies/3']),
        ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class),
        Serializer\Groups(['Currency-read'])
    ]
    protected Collection $children;

    #[
        ApiProperty(description: 'Code ', required: true, example: 'EUR'),
        Assert\Length(exactly: 3),
        Assert\NotBlank,
        ORM\Column(type: 'char', length: 3, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:unit', 'write:unit'])
    ]
    protected ?string $code = null;

    protected ?string $name = null;

    #[
        ApiProperty(description: 'Parent ', readableLink: false, example: '/api/currencies/1'),
        ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children'),
        Serializer\Groups(['Currency-read', 'Currency-write'])
    ]
    protected $parent;

    #[
        ApiProperty(description: 'Active', example: true),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['Currency-read', 'Currency-write'])
    ]
    private bool $active = false;

    #[
        ApiProperty(description: 'Nom', example: 'Euro'),
        Serializer\Groups(['Currency-read'])
    ]
    final public function getName(): ?string {
        return !empty($this->getCode()) ? Currencies::getName($this->getCode()) : null;
    }

    #[
        ApiProperty(description: 'Symbole', example: '€'),
        Serializer\Groups(['Currency-read'])
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
