<?php

namespace App\Entity\Selling\Customer;

use App\Entity\Event as AbstractEvent;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiResource;

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
    private $customer;

    public function getCustomer(): Customer {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void {
        $this->customer = $customer;
    }
}
