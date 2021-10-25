<?php

namespace App\Entity\Management;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['days' => 'partial', 'daysAfterEndOfMonth' => 'partial', 'endOfMonth' => 'exact', 'name' => 'partial']),
    ApiResource(
        description: 'InvoiceTimeDue',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les délais de facturation',
                    'summary' => 'Récupère les délais de facturation',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un délai de facturation',
                    'summary' => 'Créer un délai de facturation',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un délai de facturation',
                    'summary' => 'Supprime un délai de facturation',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un délai de facturation',
                    'summary' => 'Modifie un délai de facturation',
                ]
            ]
        ],
        shortName: 'InvoiceTimeDue',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_MANAGEMENT_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:invoicetimedue', 'write:name'],
            'openapi_definition_name' => 'InvoiceTimeDue-write'
        ],
        normalizationContext: [
            'groups' => ['read:invoicetimedue', 'read:id', 'read:name'],
            'openapi_definition_name' => 'InvoiceTimeDue-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'timeslot')
]
class InvoiceTimeDue extends Entity {
    #[
        ApiProperty(description: 'jours ', example: '60'),
        ORM\Column( type:"smallint", nullable: true),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])

    ]
    private ?int $days = null;

    #[
        ApiProperty(description: 'joursAprèsFinMois ', example: '0'),
        ORM\Column( type:"smallint", nullable: true),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])

    ]
    private ?int $daysAfterEndOfMonth = null;

    #[
        ApiProperty(description: 'fin du mois ', example: 'True'),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])
    ]
    private ?int $endOfMonth = null;
   
    #[
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank
        ]
    private ?string $name;

  
    
    public function getName()
    {
        return $this->name;
    }

    
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

   
    public function getDays()
    {
        return $this->days;
    }

  
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    
    public function getDaysAfterEndOfMonth()
    {
        return $this->daysAfterEndOfMonth;
    }

  
    public function setDaysAfterEndOfMonth($daysAfterEndOfMonth)
    {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;

        return $this;
    }

    
    public function getEndOfMonth()
    {
        return $this->endOfMonth;
    }

    
    public function setEndOfMonth($endOfMonth)
    {
        $this->endOfMonth = $endOfMonth;

        return $this;
    }
}