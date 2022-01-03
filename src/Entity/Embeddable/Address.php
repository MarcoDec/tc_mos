<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address {
    public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.email' => 'partial',
    ];

    #[
        ApiProperty(description: 'Adresse', example: '5 rue Alfred Nobel'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $address = null;

    #[
        ApiProperty(description: 'Complément d\'adresse', example: 'ZA La charrière'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $address2 = null;

    #[
        ApiProperty(description: 'Ville', example: 'Rioz'),
        Assert\Length(min: 3, max: 255),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $city = null;

    #[
        ApiProperty(description: 'Pays', example: 'FR', openapiContext: ['countries' => true]),
        Assert\Country,
        ORM\Column(length: 2, nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $country = null;

    #[
        ApiProperty(description: 'E-mail', example: 'sales@tconcept.fr'),
        Assert\Email,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $email = null;

    #[
        ApiProperty(description: 'Numéro de téléphone', example: '03 84 91 99 84'),
        AppAssert\PhoneNumber,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $phoneNumber = null;

    #[
        ApiProperty(description: 'Code postal', example: '70190'),
        AppAssert\ZipCode,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $zipCode = null;

    final public function getAddress(): ?string {
        return $this->address;
    }

    final public function getAddress2(): ?string {
        return $this->address2;
    }

    final public function getCity(): ?string {
        return $this->city;
    }

    final public function getCountry(): ?string {
        return $this->country;
    }

    final public function getEmail(): ?string {
        return $this->email;
    }

    final public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }

    final public function getZipCode(): ?string {
        return $this->zipCode;
    }

    final public function setAddress(?string $address): self {
        $this->address = $address;
        return $this;
    }

    final public function setAddress2(?string $address2): self {
        $this->address2 = $address2;
        return $this;
    }

    final public function setCity(?string $city): self {
        $this->city = $city;
        return $this;
    }

    final public function setCountry(?string $country): self {
        $this->country = $country;
        return $this;
    }

    final public function setEmail(?string $email): self {
        $this->email = $email;
        return $this;
    }

    final public function setPhoneNumber(?string $phoneNumber): self {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    final public function setZipCode(?string $zipCode): self {
        $this->zipCode = $zipCode;
        return $this;
    }

    final public function isEmpty(): bool {
        return empty($this->address)
            && empty($this->city)
            && empty($this->country)
            && empty($this->phone)
            && empty($this->zip);
    }
}
