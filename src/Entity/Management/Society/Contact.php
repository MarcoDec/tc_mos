<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Management\Society\ContactType;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template T of object
 */
#[ORM\MappedSuperclass]
abstract class Contact extends Entity {
    /** @var null|T */
    protected $society;

    #[
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Défaut', example: false),
        ORM\Column(name: '`default`', options: ['default' => false]),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private bool $default = false;

    #[
        ApiProperty(description: 'Type', example: ContactType::TYPE_PURCHASING, openapiContext: ['enum' => ContactType::TYPES]),
        ORM\Column(type: 'contact', options: ['default' => ContactType::TYPE_ACCOUNTING]),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private ?string $kind = ContactType::TYPE_PURCHASING;

    #[
        ApiProperty(description: 'Numéro de téléphone mobile', example: '06 06 06 07 07'),
        ORM\Column(length: 18, nullable: true),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private ?string $mobile = null;

    #[
        ApiProperty(description: 'Prénom', example: 'Matthieu'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Nom', example: 'Henri'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:contact', 'write:contact'])
    ]
    private ?string $surname = null;

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getMobile(): ?string {
        return $this->mobile;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    /**
     * @return null|T
     */
    final public function getSociety() {
        return $this->society;
    }

    final public function getSurname(): ?string {
        return $this->surname;
    }

    final public function isDefault(): bool {
        return $this->default;
    }

    /**
     * @return $this
     */
    final public function setAddress(Address $address): self {
        $this->address = $address;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setDefault(bool $default): self {
        $this->default = $default;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setMobile(?string $mobile): self {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * @param null|T $society
     *
     * @return $this
     */
    final public function setSociety($society): self {
        $this->society = $society;
        return $this;
    }

    /**
     * @return $this
     */
    final public function setSurname(?string $surname): self {
        $this->surname = $surname;
        return $this;
    }
    #[
        ApiProperty(description: 'Nom complet', example: 'Matthieu Henri'),
        Serializer\Groups(['read:contact']),
        Serializer\SerializedName('fullName')
    ]
    final public function getFullName(): string {
        return $this->name . ' ' . $this->surname;
    }
}
