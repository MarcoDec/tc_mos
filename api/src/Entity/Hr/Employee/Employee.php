<?php

declare(strict_types=1);

namespace App\Entity\Hr\Employee;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Entity;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Employé',
        operations: [
            new Post(
                uriTemplate: '/login',
                status: JsonResponse::HTTP_NO_CONTENT,
                openapiContext: [
                    'description' => 'Connexion',
                    'responses' => [
                        204 => ['description' => 'Connexion réussie'],
                        400 => ['description' => 'none'],
                        401 => ['description' => 'Unauthorized'],
                        422 => ['description' => 'none']
                    ],
                    'summary' => 'Connexion'
                ],
                read: false,
                deserialize: false,
                validate: false,
                write: false,
                serialize: false
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        denormalizationContext: ['groups' => ['auth'], 'swagger_definition_name' => 'auth']
    ),
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends Entity implements PasswordAuthenticatedUserInterface, UserInterface {
    #[
        ApiProperty(description: 'Mot de passe', example: 'super'),
        ORM\Column(type: 'char', length: 60, nullable: true),
        Serializer\Groups('auth')
    ]
    private ?string $password = null;

    #[
        ApiProperty(description: 'Identifiant', example: 'super'),
        ORM\Column(length: 20, nullable: true),
        Serializer\Groups('auth')
    ]
    private ?string $username = null;

    public function eraseCredentials(): void {
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    /** @return string[] */
    public function getRoles(): array {
        return [];
    }

    public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }
}
