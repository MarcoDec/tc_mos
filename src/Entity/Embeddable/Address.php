<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address {
    final public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.country' => 'partial',
        'address.email' => 'partial'
    ];
    final public const sorter = [
        'address.address',
        'address.address2',
        'address.city',
        'address.country',
        'address.email'
    ];

    #[
        ApiProperty(
            description: 'Adresse',
            example: '5 rue Alfred Nobel',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/streetAddress'], 'format' => 'streetAddress']
        ),
        Assert\Length(min: 10, max: 160),
        ORM\Column(length: 160, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $address = null;

    #[
        ApiProperty(
            description: 'Complément d\'adresse',
            example: 'ZA La charrière',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/streetAddress'], 'format' => 'streetAddress']
        ),
        Assert\Length(min: 2, max: 110),
        ORM\Column(length: 110, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $address2 = null;

    #[
        ApiProperty(
            description: 'Ville',
            example: 'Rioz',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/addressLocality'], 'format' => 'addressLocality']
        ),
        Assert\Length(min: 3, max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $city = null;

    #[
        ApiProperty(
            description: 'Pays',
            example: 'FR',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/addressLocality'], 'format' => 'addressLocality']
        ),
        Assert\Country,
        Assert\Length(exactly: 2),
        ORM\Column(type: 'char', length: 2, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $country = null;

    #[
        ApiProperty(description: 'E-mail', example: 'sales@tconcept.fr', openapiContext: ['format' => 'email']),
        Assert\Email,
        Assert\Length(min: 5, max: 80),
        ORM\Column(length: 80, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $email = null;

    #[
        ApiProperty(
            description: 'Numéro de téléphone',
            example: '03 84 91 99 84',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/telephone'], 'format' => 'telephone']
        ),
        AppAssert\PhoneNumber,
        Assert\Length(max: 18),
        ORM\Column(length: 18, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
    ]
    private ?string $phoneNumber = null;

    #[
        ApiProperty(
            description: 'Code postal',
            example: '70190',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/postalCode'], 'format' => 'postalCode']
        ),
        AppAssert\ZipCode,
        Assert\Length(min: 2, max: 10),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups(['read:address', 'write:address', 'read:society:collection'])
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

    final public function isEmpty(): bool {
        return empty($this->address)
            && empty($this->city)
            && empty($this->country)
            && empty($this->phone)
            && empty($this->zip);
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
