<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use App\Entity\Purchase\Supplier\Contact as SupplierContact;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Selling\Customer\Contact as CustomerContact;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Contact extends Entity {
    use AddressTrait;
    use NameTrait;

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
    public const TYPES = ['customer' => CustomerContact::class, 'supplier' => SupplierContact::class];

    #[
        Assert\Valid,
        ORM\Embedded(Address::class),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    protected Address $address;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Henri'),
        Assert\NotBlank,
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    /**
     * @var Customer|null|Supplier
     */
    protected $society;

    #[
        ApiProperty(description: 'Defaut ?', required: true, example: false),
        ORM\Column(type: 'boolean', options: ['default' => false]),
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
        ORM\Column(type: 'string', options: ['default' => self::KIND_ACCOUNTING], nullable: true),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private ?string $kind = self::KIND_ACCOUNTING;

    #[
        ApiProperty(description: 'Numéro de téléphone mobile', example: '06 06 06 07 07'),
        ORM\Column(nullable: true, type: 'string'),
        Serializer\Groups(['read:society-contact', 'write:society-contact'])
    ]
    private ?string $mobile = null;

    final public function getFirstname(): ?string {
        return $this->firstname;
    }

    final public function getKing(): ?string {
        return $this->kind;
    }

    final public function getMobile(): ?string {
        return $this->mobile;
    }

    /**
     * @return Customer|null|Supplier
     */
    final public function getSociety() {
        return $this->society;
    }

    public function isDefault(): bool {
        return $this->default;
    }

    public function setDefault(bool $default): void {
        $this->default = $default;
    }

    final public function setFirstname(?string $firstname): self {
        $this->firstname = $firstname;
        return $this;
    }

    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    final public function setMobile(?string $mobile): self {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @param Customer|null|Supplier $society
     *
     * @return $this
     */
    final public function setSociety($society): self {
        $this->society = $society;
        return $this;
    }
}
