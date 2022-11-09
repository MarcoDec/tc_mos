<?php

declare(strict_types=1);

namespace App\Entity\Hr\Employee;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Collection;
use App\Doctrine\Type\Hr\Employee\Role;
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
                shortName: 'Auth',
                read: false,
                deserialize: false,
                validate: false,
                write: false,
                serialize: false
            ),
            new Post(
                uriTemplate: '/logout',
                status: JsonResponse::HTTP_NO_CONTENT,
                openapiContext: [
                    'description' => 'Déconnexion',
                    'requestBody' => ['content' => []],
                    'responses' => [
                        204 => ['description' => 'Déconnexion réussie'],
                        400 => ['description' => 'none'],
                        422 => ['description' => 'none']
                    ],
                    'summary' => 'Déconnexion'
                ],
                shortName: 'Auth',
                read: false,
                deserialize: false,
                validate: false,
                write: false,
                serialize: false
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        denormalizationContext: ['groups' => ['auth']]
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

    /** @var Collection<int, Role> */
    #[ORM\Column(type: 'role')]
    private Collection $roles;

    #[
        ApiProperty(description: 'Identifiant', example: 'super'),
        ORM\Column(length: 20, nullable: true),
        Serializer\Groups('auth')
    ]
    private ?string $username = null;

    public function __construct() {
        $this->roles = new Collection([]);
    }

    public function addRole(Role $role): self {
        if ($this->roles->contains($role) === false) {
            $this->roles = $this->roles->push($role);
        }
        return $this;
    }

    public function eraseCredentials(): void {
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    /** @return string[] */
    public function getRoles(): array {
        return $this->roles->map(static fn (Role $role): string => $role->value)->values();
    }

    public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function removeRole(Role $role): self {
        if ($this->roles->contains($role)) {
            $this->roles->remove($role);
        }
        return $this;
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
