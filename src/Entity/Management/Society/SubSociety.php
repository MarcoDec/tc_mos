<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\Entity;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SocietyTrait;
use App\Entity\Management\Currency;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Embeddable\Address;

#[ORM\MappedSuperclass]
abstract class SubSociety extends Entity {
    use AddressTrait;
    use NameTrait;
    use SocietyTrait;

    #[
        ApiProperty(description: 'Monnaie', required: true, readLink: false, example: '/api/currencies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Currency::class),
        Assert\NotBlank,
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private Currency $currency;

    #[
        ApiProperty(description: 'Société', required: true, readLink: false, example: '/api/currencies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Society::class),
        Assert\NotBlank,
        Serializer\Groups(['read:society', 'write:society'])
    ]
    private Society $society;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'TConcept'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name', 'read:society:collection'])
    ]
    protected ?string $name = null;

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSociety(): ?Society
    {
        return $this->society;
    }

    public function setSociety(?Society $society): self
    {
        $this->society = $society;

        return $this;
    }

    final public function getAddress(): Address {
        return $this->address->isEmpty() && $this->society ? $this->society->getAddress() : $this->address;
    }

    final public function getName(): ?string {
        return $this->name ?? ($this->society ? $this->society->getName() : null);
    }

}
