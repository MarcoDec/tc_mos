<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Validator\ZipCode;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address {
    public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.country' => 'partial',
        'address.email' => 'partial',
    ];

    #[
        ApiProperty(description: 'Adresse', example: '5 rue Alfred Nobel'),
        Assert\Length(min: 3, max: 100),
        ORM\Column(length: 100, nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $address = null;

    #[
        ApiProperty(description: 'Complément d\'adresse', example: 'ZA La charrière'),
        Assert\Length(min: 3, max: 100),
        ORM\Column(length: 100, nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $address2 = null;

    #[
        ApiProperty(description: 'Ville', example: 'Rioz'),
        Assert\Length(min: 3, max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $city = null;

    #[
        ApiProperty(description: 'Pays', example: 'FR'),
        Assert\Country,
        ORM\Column(type: 'char', length: 2, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $country = null;

    #[
        ApiProperty(description: 'E-mail', example: 'sales@tconcept.fr'),
        Assert\Email,
        Assert\Length(min: 5, max: 100),
        ORM\Column(length: 100, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?string $email = null;

    #[
        ApiProperty(description: 'Numéro de téléphone', example: '03 84 91 99 84'),
        Assert\Length(min: 10, max: 20),
        ORM\Column(length: 20, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:address', 'write:address']),
        ZipCode
    ]
    private ?string $phoneNumber = null;

    #[
        ApiProperty(description: 'Code postal', example: '70190'),
        Assert\Length(exactly: 5),
        ORM\Column(type: 'char', length: 5, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(['read:address', 'write:address']),
        ZipCode
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
}
