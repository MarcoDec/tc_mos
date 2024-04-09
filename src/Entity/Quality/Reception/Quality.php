<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Supplier\Supplier;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        description: 'Qualité',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les contrôles réception qualité',
                    'summary' => 'Récupère les contrôles réception qualité',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Ajoute un contrôle réception qualité',
                    'summary' => 'Ajoute un contrôle réception qualité'
                ]
            ]
        ],
        itemOperations: [
            'get' => NO_ITEM_GET_OPERATION,
            'patch',
            'delete'
        ],
        shortName: 'Quality',
        denormalizationContext: [
            'groups' => ['write:quality', 'write:measure'],
            'openapi_definition_name' => 'Quality-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:quality', 'read:measure'],
            'openapi_definition_name' => 'Quality-read'
        ],
        paginationClientEnabled: true
    ),
    \Doctrine\ORM\Mapping\Entity
]
class Quality extends Entity
{
     #[
         ApiProperty(description: 'Commentaires du contrôle qualité réception'),
         Column(length:255, nullable: true, type: 'string'),
         Groups(['read:quality', 'write:quality'])
     ]
    private string $comments;

     #[
         ApiProperty(description: 'Composant contrôlé', readableLink: false, example: '/api/components/1'),
         ManyToOne(targetEntity: Component::class),
         Groups(['read:quality', 'write:quality'])
     ]
    private Component $component;

     #[
         ApiProperty(description: 'Elément contrôlé ??', example: '1'),
         Column(nullable: true, type: 'integer'),
         Groups(['read:quality', 'write:quality'])
     ]
    private int $quality;

     #[
         ApiProperty(description: 'Fournisseur du composant', readableLink: false, example: '/api/suppliers/1'),
         ManyToOne(targetEntity: Supplier::class),
         Groups(['read:quality', 'write:quality'])
     ]
    private Supplier $supplier;

     #[
         ApiProperty(description: 'Texte Généré automatiquement'),
         Column(length: 255, nullable: true, type: 'string'),
         Groups(['read:quality', 'write:quality'])
     ]
    private string $text;

    public function getComments(): ?string {
        return $this->comments;
    }

    public function getComponent(): ?Component {
        return $this->component;
    }

    public function getQuality(): ?int {
        return $this->quality;
    }

    public function getSupplier(): ?Supplier {
        return $this->supplier;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function getTitle(): string {
        return $this->getText();
    }

    public function setComments(string $comments): self {
        $this->comments = $comments;
        return $this;
    }

    public function setComponent(Component $component): self {
        $this->component = $component;
        return $this;
    }

    public function setQuality(int $quality): self {
        $this->quality = $quality;
        return $this;
    }

    public function setSupplier(?Supplier $supplier): self {
        $this->supplier = $supplier;
        return $this;
    }

    public function setText(string $text): self {
        $this->text = $text;
        return $this;
    }
}
