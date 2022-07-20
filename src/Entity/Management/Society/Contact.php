<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use App\Entity\Selling\Customer\Contact as CustomerContact;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @template T of object
 */
abstract class Contact extends Entity {
    public const KIND_ACCOUNTING = 'comptabilité';
    public const KIND_COSTING = 'chiffrage';
    public const KIND_DIRECTION = 'direction';
    public const KIND_ENGINEERING = 'ingénierie';
    public const KIND_MANUFACTURING = 'fabrication';
    public const KIND_PURCHASING = 'achat';
    public const KIND_QUALITY = 'qualité';
    public const KIND_SELLING = 'commercial';
    public const KIND_SUPPLYING = 'approvisionnement';
    public const KINDS = [self::KIND_ACCOUNTING, self::KIND_SUPPLYING, self::KIND_SELLING, self::KIND_COSTING, self::KIND_ENGINEERING, self::KIND_DIRECTION, self::KIND_QUALITY, self::KIND_MANUFACTURING, self::KIND_PURCHASING];
    public const TYPES = ['customer' => CustomerContact::class];

    /** @var null|T */
    protected $society;

    #[
        Assert\Valid,
        ORM\Embedded,
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private Address $address;

    #[
        ApiProperty(description: 'Defaut ?', required: true, example: false),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private bool $default = false;

    #[
        ApiProperty(description: 'Prénom', example: 'Matthieu'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private ?string $firstname = null;

    #[
        ApiProperty(description: 'Type', required: false, example: self::KIND_ACCOUNTING),
        ORM\Column(options: ['default' => self::KIND_ACCOUNTING]),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private ?string $kind = self::KIND_ACCOUNTING;

    #[
        ApiProperty(description: 'Numéro de téléphone mobile', example: '06 06 06 07 07'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private ?string $mobile = null;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Henri'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?string $name = null;

    final public function getAddress(): Address {
        return $this->address;
    }

    final public function getFirstname(): ?string {
        return $this->firstname;
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
    final public function setFirstname(?string $firstname): self {
        $this->firstname = $firstname;
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
}
