<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Hr\Employee\NotificationCategoryType;
use App\Entity\Entity;
use App\Repository\Hr\Employee\NotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Notifications',
        collectionOperations: [
            'delete' => [
                'deserialize' => false,
                'method' => 'DELETE',
                'openapi_context' => [
                    'description' => 'Supprime les notifications de l\'utilisateur dans la catégorie',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'category',
                        'required' => true,
                        'schema' => [
                            'enum' => NotificationCategoryType::TYPES,
                            'type' => 'string'
                        ]
                    ]],
                    'summary' => 'Supprime les notifications de l\'utilisateur dans la catégorie'
                ],
                'read' => false,
                'route_name' => 'api_notifications_delete_collection',
                'serialize' => false,
                'validate' => false,
                'write' => false
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les notifications de l\'utilisateur courant',
                    'summary' => 'Récupère les notifications de l\'utilisateur courant'
                ]
            ],
            'patch' => [
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Marque les notifications de l\'utilisateur dans la catégorie',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'category',
                        'required' => true,
                        'schema' => [
                            'enum' => NotificationCategoryType::TYPES,
                            'type' => 'string'
                        ]
                    ]],
                    'requestBody' => null,
                    'summary' => 'Marque les notifications de l\'utilisateur dans la catégorie'
                ],
                'read' => false,
                'route_name' => 'api_notifications_patch_collection',
                'validate' => false,
                'write' => false
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime la notification',
                    'summary' => 'Supprime la notification'
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'deserialize' => false,
                'openapi_context' => [
                    'description' => 'Marque la notification comme lue',
                    'requestBody' => null,
                    'summary' => 'Marque la notification comme lue'
                ]
            ]
        ],
        paginationEnabled: false
    ),
    ORM\Entity(repositoryClass: NotificationRepository::class)
]
class Notification extends Entity {
    #[
        ApiProperty(description: 'Catégorie', example: NotificationCategoryType::TYPE_DEFAULT, openapiContext: ['enum' => NotificationCategoryType::TYPES]),
        ORM\Column(type: 'notification_category', options: ['default' => NotificationCategoryType::TYPE_DEFAULT])
    ]
    private ?string $category = NotificationCategoryType::TYPE_DEFAULT;

    #[ORM\Column(type: 'datetime_immutable', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: '`read`', options: ['default' => false])]
    private bool $read = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subject = null;

    #[
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne
    ]
    private ?Employee $user = null;

    public function __construct() {
        $this->createdAt = new DateTimeImmutable();
    }

    final public function getCategory(): ?string {
        return $this->category;
    }

    final public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    final public function getSubject(): ?string {
        return $this->subject;
    }

    final public function getUser(): ?Employee {
        return $this->user;
    }

    final public function isRead(): bool {
        return $this->read;
    }

    final public function setCategory(?string $category): self {
        $this->category = $category;
        return $this;
    }

    final public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    final public function setRead(bool $read): self {
        $this->read = $read;
        return $this;
    }

    final public function setSubject(?string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    final public function setUser(?Employee $user): self {
        $this->user = $user;
        return $this;
    }
}
