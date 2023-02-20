<?php

declare(strict_types=1);

namespace App\Entity\Hr\Employee;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\Hr\Employee\Notification\CategoryReadAction;
use App\Controller\Hr\Employee\Notification\CategoryRemoveAction;
use App\Doctrine\Type\Hr\Employee\EnumNotificationType;
use App\Entity\Entity;
use App\Repository\Hr\Employee\NotificationRepository;
use App\Security\Voter\NotificationVoter;
use App\State\Hr\Employee\Notification\NotificationProvider;
use App\State\Hr\Employee\Notification\ReadProcessor;
use App\State\Hr\Employee\Notification\RemoveProcessor;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Notification',
        operations: [
            new GetCollection(
                openapiContext: [
                    'description' => 'Récupère les notifications de l\'utilisateur courant',
                    'summary' => 'Récupère les notifications de l\'utilisateur courant'
                ],
                provider: NotificationProvider::class
            ),
            new Patch(
                uriTemplate: '/notifications/all/{category}',
                uriVariables: ['category'],
                status: JsonResponse::HTTP_NO_CONTENT,
                controller: CategoryReadAction::class,
                openapiContext: [
                    'description' => 'Marque toutes les notifications d\'une catégorie comme lue',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'category',
                        'required' => true,
                        'schema' => ['enum' => EnumNotificationType::ENUM, 'type' => 'string']
                    ]],
                    'requestBody' => ['content' => []],
                    'summary' => 'Marque toutes les notifications d\'une catégorie comme lue'
                ],
                securityPostDenormalize: NotificationVoter::GRANTED_NOTIFICATION_READ,
                read: false,
                deserialize: false,
                validate: false,
                write: false,
                serialize: false
            ),
            new Delete(
                uriTemplate: '/notifications/all/{category}',
                uriVariables: ['category'],
                controller: CategoryRemoveAction::class,
                openapiContext: [
                    'description' => 'Supprime toutes les notifications d\'une catégorie',
                    'parameters' => [[
                        'in' => 'path',
                        'name' => 'category',
                        'required' => true,
                        'schema' => ['enum' => EnumNotificationType::ENUM, 'type' => 'string']
                    ]],
                    'requestBody' => ['content' => []],
                    'summary' => 'Supprime toutes les notifications d\'une catégorie'
                ],
                securityPostDenormalize: NotificationVoter::GRANTED_NOTIFICATION_READ,
                read: false,
                deserialize: false,
                validate: false,
                write: false,
                serialize: false
            ),
            new Delete(
                openapiContext: ['description' => 'Supprimer une notification', 'summary' => 'Supprimer une notification'],
                securityPostDenormalize: NotificationVoter::GRANTED_NOTIFICATION_READ,
                processor: RemoveProcessor::class
            ),
            new Patch(
                openapiContext: [
                    'description' => 'Marque une notification comme lue',
                    'requestBody' => ['content' => []],
                    'summary' => 'Marque une notification comme lue'
                ],
                securityPostDenormalize: NotificationVoter::GRANTED_NOTIFICATION_READ,
                deserialize: false,
                processor: ReadProcessor::class
            )
        ],
        outputFormats: 'jsonld',
        normalizationContext: [
            'groups' => ['id', 'notification-read'],
            'skip_null_values' => false,
            'openapi_definition_name' => 'notification-read'
        ],
        order: ['createdAt' => 'desc'],
        paginationEnabled: false
    ),
    ORM\Entity(repositoryClass: NotificationRepository::class)
]
class Notification extends Entity {
    #[ORM\Column(type: 'notification', options: ['default' => EnumNotificationType::DEFAULT])]
    private EnumNotificationType $category = EnumNotificationType::TYPE_DEFAULT;

    #[
        ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP']),
        Serializer\Groups('notification-read')
    ]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: '`read`', options: ['default' => false]), Serializer\Groups('notification-read')]
    private bool $read = false;

    #[ORM\Column(length: 50), Serializer\Groups('notification-read')]
    private ?string $subject = null;

    #[ORM\JoinColumn(nullable: false), ORM\ManyToOne]
    private ?Employee $user = null;

    public function __construct() {
        $this->createdAt = new DateTimeImmutable();
    }

    #[
        ApiProperty(
            description: 'Catégorie',
            default: EnumNotificationType::DEFAULT,
            example: EnumNotificationType::DEFAULT,
            openapiContext: ['enum' => EnumNotificationType::DEFAULT]
        ),
        Serializer\Groups('notification-read')
    ]
    public function getCategory(): string {
        return $this->category->value;
    }

    public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function getUser(): ?Employee {
        return $this->user;
    }

    public function isRead(): bool {
        return $this->read;
    }

    public function setCategory(string $category): self {
        $this->category = EnumNotificationType::from($category);
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setRead(bool $read): self {
        $this->read = $read;
        return $this;
    }

    public function setSubject(?string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    public function setUser(?Employee $user): self {
        $this->user = $user;
        return $this;
    }
}
