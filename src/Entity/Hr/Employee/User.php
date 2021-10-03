<?php

namespace App\Entity\Hr\Employee;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Repository\Hr\Employee\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\MappedSuperclass(repositoryClass: UserRepository::class)]
abstract class User extends Entity implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Column(length: 180, unique: true)]
    private string $username;
    #[ORM\Embedded(class: Roles::class)]
    private Roles $roles;
    #[ORM\Column]
    private string $password;

    public function __construct() {
        $this->roles = new Roles();
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return $this->username;
    }

    /**
     * @return string[]
     * @see UserInterface
     *
     */
    public function getRoles(): array {
        return $this->roles->getRoles();
    }

    final public function addRole(string $role): self {
        $this->roles->addRole($role);
        return $this;
    }

    final public function removeRole(string $role): self {
        $this->roles->removeRole($role);
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
