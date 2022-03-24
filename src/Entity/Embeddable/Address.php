<?php

namespace App\Entity\Embeddable;

use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address {
    final public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.country' => 'partial',
        'address.email' => 'partial',
    ];

    #[
        Assert\Length(min: 10, max: 50),
        ORM\Column(length: 50, nullable: true)
    ]
    private ?string $address = null;

    #[
        Assert\Length(min: 10, max: 50),
        ORM\Column(length: 50, nullable: true)
    ]
    private ?string $address2 = null;

    #[
        Assert\Length(min: 3, max: 50),
        ORM\Column(length: 50, nullable: true)
    ]
    private ?string $city = null;

    #[
        Assert\Country,
        Assert\Length(exactly: 2),
        ORM\Column(type: 'char', length: 2, nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $country = null;

    #[
        Assert\Email,
        Assert\Length(min: 5, max: 60),
        ORM\Column(length: 60, nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $email = null;

    #[
        AppAssert\PhoneNumber,
        Assert\Length(min: 10, max: 20),
        ORM\Column(length: 20, nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $phoneNumber = null;

    #[
        AppAssert\ZipCode,
        Assert\Length(min: 2, max: 10),
        ORM\Column(length: 10, nullable: true, options: ['charset' => 'ascii'])
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
