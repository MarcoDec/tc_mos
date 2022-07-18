<?php

namespace App\Entity\Management\Society\Company;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Management\Society\Society;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Compagnie',
        collectionOperations: [
            'get-options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:company:option'],
                    'openapi_definition_name' => 'Company-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les compagnies pour les select',
                    'summary' => 'Récupère les compagnies pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/companies/options'
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION]
    ),
    ORM\Entity
]
class Company extends Entity {
    #[
        Assert\NotBlank,
        ORM\Column(nullable: true)
    ]
    private ?string $name = null;

    #[
        Assert\NotBlank,
        ORM\ManyToOne
    ]
    private ?Society $society = null;

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    #[Serializer\Groups(['read:company:option'])]
    final public function getText(): ?string {
        return $this->getName();
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }
}
