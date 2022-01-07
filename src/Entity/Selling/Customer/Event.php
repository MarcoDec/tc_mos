<?php

namespace App\Entity\Selling\Customer;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Event as AbstractEvent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ApiResource(
    description: 'Evenement',
    shortName: 'CustomerEvent'
),
    ORM\Entity
]
class Event extends AbstractEvent {
    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class, inversedBy: 'events'),
        Serializer\Groups(['read:event', 'write:event'])
    ]
    private Customer $customer;

    public function getCustomer(): Customer {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void {
        $this->customer = $customer;
    }
}
