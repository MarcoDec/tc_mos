<?php

namespace App\Entity\Embeddable;

use App\ApiPlatform\Core\Annotation\ApiProperty;
use App\ApiPlatform\Core\Annotation\ApiSerializerGroups;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adresse.
 */
#[
    ApiSerializerGroups(inheritedRead: ['Address-write' => ['Address-read']], write: ['Address-write']),
    ORM\Embeddable
]
class Address {
    final public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.country' => 'partial',
        'address.email' => 'partial',
    ];

    /**
     * @var null|string Adresse
     */
    #[
        ApiProperty(example: '5 rue Alfred Nobel', format: 'streetAddress'),
        Assert\Length(min: 10, max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private ?string $address = null;

    /**
     * @var null|string Complément d'adresse
     */
    #[
        ApiProperty(example: 'ZA La charrière', format: 'streetAddress'),
        Assert\Length(min: 10, max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private ?string $address2 = null;

    /**
     * @var null|string Ville
     */
    #[
        ApiProperty(example: 'Rioz', format: 'addressLocality'),
        Assert\Length(min: 3, max: 50),
        ORM\Column(length: 50, nullable: true),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private ?string $city = null;

    /**
     * @var string Pays
     */
    #[
        ApiProperty(format: 'addressLocality'),
        Assert\Country,
        Assert\Length(exactly: 2),
        ORM\Column(type: 'char', length: 2, options: ['charset' => 'ascii', 'default' => 'FR']),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private string $country = 'FR';

    /**
     * @var null|string E-mail
     */
    #[
        ApiProperty(example: 'sales@tconcept.fr'),
        Assert\Email,
        Assert\Length(min: 5, max: 60),
        ORM\Column(length: 60, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private ?string $email = null;

    /**
     * @var null|string Numéro de téléphone
     */
    #[
        ApiProperty(example: '03 84 91 99 84'),
        AppAssert\PhoneNumber,
        Assert\Length(min: 10, max: 20),
        ORM\Column(length: 20, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
    ]
    private ?string $phoneNumber = null;

    /**
     * @var null|string Code postal
     */
    #[
        ApiProperty(example: '70190'),
        AppAssert\ZipCode,
        Assert\Length(min: 2, max: 10),
        ORM\Column(length: 10, nullable: true, options: ['charset' => 'ascii']),
        Serializer\Groups(groups: ['Address-read', 'Address-write'])
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

    final public function getCountry(): string {
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

    final public function setCountry(string $country): self {
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
