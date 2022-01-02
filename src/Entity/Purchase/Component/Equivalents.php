<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les équivalences',
                    'summary' => 'Récupère les équivalences'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une équivalence',
                    'summary' => 'Créer une équivalence'
                ],
                'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une équivalence',
                    'summary' => 'Supprime une équivalence'
                ]
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une équivalence',
                    'summary' => 'Récupère une équivalence',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifier une équivalence',
                    'summary' => 'Modifier une équivalence',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PURCHASE_READER.'\')'
        ],
        normalizationContext: [
            'groups' => ['read:component'],
            'openapi_definition_name' => 'Equivalents-read'
        ]
    ),
    ORM\Entity
]
class Equivalents extends Entity {
    /**
     * @var Collection<int, Component>
     */
    #[
        ApiProperty(description: 'Composants', required: false, readableLink: false, example: ['/api/components/5', '/api/components/14']),
        ORM\OneToMany(mappedBy: 'equivalents', targetEntity: Component::class),
        Serializer\Groups(['read:component']),
    ]
    private Collection $components;

    public function __construct() {
        $this->components = new ArrayCollection();
    }

    public static function setEquivalents(Component $a, Component $b): void {
        if ($a->getEquivalents() === $b->getEquivalents()) {
            if ($a->getEquivalents() === null) {
                (new self())->addComponent($a)->addComponent($b);
            }
        } elseif ($a->getEquivalents() === null && $b->getEquivalents() !== null) {
            $b->getEquivalents()->addComponent($a);
        } elseif ($a->getEquivalents() !== null && $b->getEquivalents() === null) {
            $a->getEquivalents()->addComponent($b);
        } else {
            $a->getEquivalents()->merge($b->getEquivalents());
        }
    }

    final public function addComponent(Component $component): self {
        if (!$this->components->contains($component)) {
            $this->components->add($component);
            $component->setEquivalents($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Component>
     */
    final public function getComponents(): Collection {
        return $this->components;
    }

    final public function removeComponent(Component $component): self {
        if ($this->components->contains($component)) {
            $this->components->removeElement($component);
            if ($component->getEquivalents() === $this) {
                $component->setEquivalents(null);
            }
        }
        return $this;
    }

    final protected function merge(self $equivalents): self {
        foreach ($equivalents->getComponents() as $component) {
            $equivalents->removeComponent($component);
            $this->addComponent($component);
        }
        return $this;
    }
}
