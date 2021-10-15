<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        collectionOperations: [],
        itemOperations: ['get' => []],
        normalizationContext: ['groups' => ['read:user'], 'openapi_definition_name' => 'User-read']
    ),
    ORM\MappedSuperclass(repositoryClass: UserRepository::class)
]
abstract class User extends Entity implements PasswordAuthenticatedUserInterface, UserInterface {
    #[
        ApiProperty(identifier: false),
        ORM\Column(type: 'integer', options: ['unsigned' => true]),
        ORM\GeneratedValue,
        ORM\Id
    ]
    protected int $id;

    #[
        ORM\Embedded(class: Roles::class),
        Serializer\Groups(['read:user'])
    ]
    private Roles $embRoles;

    #[ORM\Column]
    private string $password;

    #[
        ApiProperty(description: 'identifiant', identifier: true, example: 'super'),
        ORM\Column(length: 180, unique: true),
        Serializer\Groups(['read:user'])
    ]
    private string $username;

    #[Pure]
    final public function __construct() {
        $this->embRoles = new Roles();
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

    final public function getEmbRoles(): Roles {
        return $this->embRoles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    final public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return string[]
     *
     * @see UserInterface
     */
    #[Pure]
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    final public function getUserIdentifier(): string {
        return $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    final public function getUsername(): string {
        return $this->username;
    }

    final public function removeRole(string $role): self {
        $this->embRoles->removeRole($role);
        return $this;
    }

    final public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }
}
