<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

abstract class Address extends Entity {
    use AddressTrait;
    use NameTrait;

    public const TYPES = ['billing' => BillingAddress::class, 'delivery' => DeliveryAddress::class];

    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class),
        Serializer\Groups(['read:address', 'write:address'])
    ]
    private ?Customer $customer;

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }
}
