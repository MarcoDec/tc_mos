<?php

namespace App\Entity\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Api\Token;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Attachment\EmployeeAttachment;
use App\Entity\Traits\NameTrait;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

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
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:employee', 'read:name', 'read:user'],
            'openapi_definition_name' => 'Employee-read'
        ]
    ),
    ORM\Entity(repositoryClass: EmployeeRepository::class)
]
class Employee extends Entity implements PasswordAuthenticatedUserInterface, UserInterface {
    use NameTrait;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeAttachment::class)]
    private Collection $attachments;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Super'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /**
     * @var Collection<int, Token>
     */
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Token::class)]
    private Collection $apiTokens;

    #[ORM\Embedded]
    private Roles $embRoles;

    #[ORM\Column]
    private ?string $password = null;

    #[
        ApiProperty(description: 'identifiant', example: 'super'),
        ORM\Column(length: 180),
        Serializer\Groups(['read:employee'])
    ]
    private ?string $username = null;

    #[Pure]
    final public function __construct() {
        $this->apiTokens = new ArrayCollection();
        $this->embRoles = new Roles();
        $this->attachments = new ArrayCollection();
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
    final public function getUserIdentifier(): ?string {
        return $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
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

    final public function setPassword(?string $password): self {
        $this->password = $password;
        return $this;
    }

    final public function setUsername(?string $username): self {
        $this->username = $username;
        return $this;
    }

   /**
    * @return ArrayCollection|Collection
    */
   public function getAttachments(): ArrayCollection|Collection
   {
      return $this->attachments;
   }

   /**
    * @param ArrayCollection|Collection $attachments
    */
   public function setAttachments(ArrayCollection|Collection $attachments): void
   {
      $this->attachments = $attachments;
   }

   /**
    * @param EmployeeAttachment $attachment
    * @return $this
    */
   public function addAttachment(EmployeeAttachment $attachment): self {
      if (!$this->attachments->contains($attachment)) {
         $this->attachments->add($attachment);
      }
      return $this;
   }

}
