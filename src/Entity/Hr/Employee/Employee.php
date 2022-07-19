<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Api\Token;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Employé',
        collectionOperations: [],
        itemOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère un employé',
                    'summary' => 'Récupère un employé'
                ]
            ],
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:employee', 'read:user'],
            'openapi_definition_name' => 'Employee-read',
            'skip_null_values' => false
        ]
    ),
    ORM\Entity
]
class Employee extends Entity implements PasswordAuthenticatedUserInterface, UserInterface {
    /** @var Collection<int, Token> */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

    #[ORM\Embedded]
    private Roles $embRoles;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Super'),
        ORM\Column(length: 30),
        Serializer\Groups(['read:employee', 'write:employee'])
    ]
    private ?string $name = null;

    #[ORM\Column(type: 'char', length: 60)]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    private ?Team $team = null;

    #[
        ApiProperty(description: 'identifiant', example: 'super'),
        ORM\Column(length: 20),
        Serializer\Groups(['read:employee'])
    ]
    private ?string $username = null;

    #[Pure]
    public function __construct() {
        $this->apiTokens = new ArrayCollection();
        $this->embRoles = new Roles();
    }

    final public function addApiToken(Token $apiToken): self {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
        }
        return $this;
    }

    final public function addRole(string $role): self {
        $this->embRoles->addRole($role);
        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Token>
     */
    final public function getApiTokens(): Collection {
        return $this->apiTokens;
    }

    final public function getCurrentApiToken(): ?Token {
        foreach ($this->apiTokens as $token) {
            if (!$token->isExpired()) {
                return $token;
            }
        }
        return null;
    }

    final public function getEmbRoles(): Roles {
        return $this->embRoles;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    final public function getPassword(): ?string {
        return $this->password;
    }

    /**
     * @return string[]
     *
     * @see UserInterface
     */
    #[
        ApiProperty(description: 'Rôles', example: [Roles::ROLE_USER]),
        Pure,
        Serializer\Groups(['read:employee'])
    ]
    final public function getRoles(): array {
        return $this->embRoles->getRoles();
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    final public function getSalt(): ?string {
        return null;
    }

    final public function getTeam(): ?Team {
        return $this->team;
    }

    #[
        ApiProperty(description: 'Token', example: '47e65f14b42a5398c1eea9125aaf93e44b1ddeb93ea2cca769ea897e0a285e4e7cfac21dee1a56396e15c1c5ee7c8d4e0bf692c83cda86a6462ad707'),
        Serializer\Groups(['read:employee'])
    ]
    final public function getToken(): ?string {
        return $this->getCurrentApiToken()?->getToken();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    final public function getUserIdentifier(): string {
        return (string) $this->username;
    }

    final public function getUsername(): ?string {
        return $this->username;
    }

    final public function removeApiToken(Token $apiToken): self {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
        }
        return $this;
    }

    final public function removeRole(string $role): self {
        $this->embRoles->removeRole($role);
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setTeam(?Team $team): self {
        $this->team = $team;
        return $this;
    }

    final public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }
}
