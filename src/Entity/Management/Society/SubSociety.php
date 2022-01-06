<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Embeddable\Address;
use App\Entity\Entity;
use App\Entity\Management\Currency;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SocietyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class SubSociety extends Entity {
    use AddressTrait;
    use NameTrait;
    use SocietyTrait;

    #[
        ApiProperty(description: 'Nom', required: true, example: 'Kaporingol'),
        Assert\NotBlank,
        ORM\Column(nullable: true),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    protected ?string $name = null;

    #[
        ApiProperty(description: 'Monnaie', required: true, readableLink: false, example: '/api/currencies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Currency::class),
        Assert\NotBlank,
        Serializer\Groups(['read:currency', 'write:currency'])
    ]
    private ?Currency $currency;

    #[
        ApiProperty(description: 'Société', required: true, readableLink: false, example: '/api/societies/2'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Society::class),
        Assert\NotBlank,
        Serializer\Groups(['read:subsociety', 'write:subsociety', 'read:society', 'write:society', 'write:customer_society'])
    ]
    private ?Society $society;

    final public function getAddress(): Address {
        return $this->address->isEmpty() && $this->society ? $this->society->getAddress() : $this->address;
    }

    public function getCurrency(): ?Currency {
        return $this->currency;
    }

    final public function getName(): ?string {
        return $this->name ?? ($this->society ? $this->society->getName() : null);
    }

    public function getSociety(): ?Society {
        return $this->society;
    }

    public function setCurrency(?Currency $currency): self {
        $this->currency = $currency;

        return $this;
    }

    public function setSociety(?Society $society): self {
        $this->society = $society;

        return $this;
    }
}
