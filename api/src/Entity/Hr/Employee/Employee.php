<?php

declare(strict_types=1);

namespace App\Entity\Hr\Employee;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Entity;
use App\Entity\Token;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Employé',
        operations: [
            new Get(
                uriTemplate: '/user',
                openapiContext: [
                    'description' => 'Récupère l\'utilisateur courant',
                    'summary' => 'Récupère l\'utilisateur courant'
                ]
            ),
            new Post(
                uriTemplate: '/login',
                openapiContext: ['description' => 'Connexion', 'summary' => 'Connexion']
            )
        ],
        inputFormats: 'json',
        outputFormats: 'jsonld',
        normalizationContext: ['groups' => ['id', 'user'], 'swagger_definition_name' => 'user'],
        denormalizationContext: ['groups' => ['auth'], 'swagger_definition_name' => 'auth']
    ),
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends Entity implements PasswordAuthenticatedUserInterface, UserInterface {
    /** @var Collection<int, Token> */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

    #[
        ApiProperty(description: 'Mot de passe', example: 'super'),
        ORM\Column(type: 'char', length: 60, nullable: true),
        Serializer\Groups('auth')
    ]
    private ?string $password = null;

    #[
        ApiProperty(description: 'Identifiant', example: 'super'),
        ORM\Column(length: 20, nullable: true),
        Serializer\Groups(['auth', 'user'])
    ]
    private ?string $username = null;

    public function __construct() {
        $this->apiTokens = new ArrayCollection();
    }

    final public function addApiToken(Token $apiToken): self {
        if ($this->apiTokens->contains($apiToken) === false) {
            $this->apiTokens->add($apiToken);
        }
        return $this;
    }

    public function eraseCredentials(): void {
    }

    /** @return Collection<int, Token> */
    public function getApiTokens(): Collection {
        return $this->apiTokens;
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

    final public function removeApiToken(Token $apiToken): self {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
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
