<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use IsoCodes\PhoneNumber;
use IsoCodes\ZipCode;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Embeddable]
class Address {
    /** @var array<string, string> */
    final public const filter = [
        'address.address' => 'partial',
        'address.address2' => 'partial',
        'address.city' => 'partial',
        'address.country' => 'partial',
        'address.email' => 'partial'
    ];

    /** @var string[] */
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
        Serializer\Groups('address')
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
        Serializer\Groups('address')
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
        Serializer\Groups('address')
    ]
    private ?string $city = null;

    #[
        ApiProperty(
            description: 'Pays',
            example: 'FR',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/addressLocality'], 'format' => 'addressLocality']
        ),
        Assert\NotBlank(groups: ['address-country-not-blank']),
        Assert\Sequentially(
            constraints: [new Assert\Length(exactly: 2), new Assert\Country()],
            groups: ['address-country']
        ),
        ORM\Column(type: 'char', length: 2, nullable: true),
        Serializer\Groups('address')
    ]
    private ?string $country = null;

    #[
        ApiProperty(description: 'E-mail', example: 'sales@tconcept.fr', openapiContext: ['format' => 'email']),
        Assert\Sequentially([new Assert\Length(min: 5, max: 80), new Assert\Email()]),
        ORM\Column(length: 80, nullable: true),
        Serializer\Groups('address')
    ]
    private ?string $email = null;

    #[
        ApiProperty(
            description: 'Numéro de téléphone',
            example: '03 84 91 99 84',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/telephone'], 'format' => 'telephone']
        ),
        Assert\Length(min: 10, max: 18, groups: ['address-phone-number']),
        ORM\Column(length: 18, nullable: true),
        Serializer\Groups('address')
    ]
    private ?string $phoneNumber = null;

    #[
        ApiProperty(
            description: 'Code postal',
            example: '70190',
            openapiContext: ['externalDocs' => ['url' => 'http://schema.org/postalCode'], 'format' => 'postalCode']
        ),
        Assert\Length(min: 2, max: 10, groups: ['address-zip-code']),
        ORM\Column(length: 10, nullable: true),
        Serializer\Groups('address')
    ]
    private ?string $zipCode = null;

    public function getAddress(): ?string {
        return $this->address;
    }

    public function getAddress2(): ?string {
        return $this->address2;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function getCountry(): ?string {
        return $this->country;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }

    public function getZipCode(): ?string {
        return $this->zipCode;
    }

    public function setAddress(?string $address): self {
        $this->address = $address;
        return $this;
    }

    public function setAddress2(?string $address2): self {
        $this->address2 = $address2;
        return $this;
    }

    public function setCity(?string $city): self {
        $this->city = $city;
        return $this;
    }

    public function setCountry(?string $country): self {
        $this->country = $country;
        return $this;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;
        return $this;
    }

    public function setPhoneNumber(?string $phoneNumber): self {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function setZipCode(?string $zipCode): self {
        $this->zipCode = $zipCode;
        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void {
        if (empty($this->phoneNumber) === false || empty($this->zipCode) === false) {
            $violations = $context->getValidator()->validate(value: $this, groups: ['address-country-not-blank']);
            if ($violations->count() > 0) {
                $context->getViolations()->addAll($violations);
                return;
            }
        }

        $violations = $context->getValidator()->validate(value: $this, groups: ['address-country']);
        if ($violations->count() > 0) {
            $context->getViolations()->addAll($violations);
            return;
        }

        if (empty($this->phoneNumber) === false) {
            $violations = $context->getValidator()->validate(value: $this, groups: ['address-phone-number']);
            if ($violations->count() > 0) {
                $context->getViolations()->addAll($violations);
            } elseif (PhoneNumber::validate($this->phoneNumber, $this->country) === false) {
                $context->buildViolation('Le numéro {{ phoneNumber }} n\'est pas un numéro de téléphone valide pour le pays {{ country }}.')
                    ->setParameters(['{{ phoneNumber }}' => $this->phoneNumber, '{{ country }}' => $this->country])
                    ->atPath('phoneNumber')
                    ->addViolation();
            }
        }

        if (empty($this->zipCode) === false) {
            $violations = $context->getValidator()->validate(value: $this, groups: ['address-zip-code']);
            if ($violations->count() > 0) {
                $context->getViolations()->addAll($violations);
            } elseif (ZipCode::validate($this->zipCode, $this->country) === false) {
                $context->buildViolation('Le code {{ code }} n\'est pas un code postal valide pour le pays {{ country }}.')
                    ->setParameters(['{{ phoneNumber }}' => $this->zipCode, '{{ country }}' => $this->country])
                    ->atPath('zipCode')
                    ->addViolation();
            }
        }
    }
}
