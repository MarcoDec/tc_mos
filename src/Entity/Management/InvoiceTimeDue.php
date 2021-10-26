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
    ORM\Table(name: 'InvoiceTimeDue')
]
class InvoiceTimeDue extends Entity {
    #[
        ApiProperty(description: 'jours ', example: '60'),
        ORM\Column( type:"smallint", options: ['default' => 0 ,'unsigned'=> true]),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])

    ]
    private ?int $days = 0;

    #[
        ApiProperty(description: 'joursAprèsFinMois ', example: '0'),
        ORM\Column( type:"smallint", options: ['default' => 0 ,'unsigned'=> true]),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])

    ]
    private ?int $daysAfterEndOfMonth = 0;

    #[
        ApiProperty(description: 'fin du mois ', example: 'True'),
        ORM\Column(options: ['default' => false]),
        Serializer\Groups(['read:invoicetimedue', 'write:invoicetimedue'])
    ]
    private ?bool $endOfMonth = false;
   
    #[
        ORM\Column,
        Serializer\Groups(['read:name', 'write:name']),
        Assert\NotBlank
        ]
    private ?string $name;

  
    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    
    public function getDays(): ?int
    {
        return $this->days;
    }

    
    public function setDays(?int $days): self
    {
        $this->days = $days;

        return $this;
    }

    
    public function getDaysAfterEndOfMonth(): ?int
    {
        return $this->daysAfterEndOfMonth;
    }

    
    public function setDaysAfterEndOfMonth(?int $daysAfterEndOfMonth): self
    {
        $this->daysAfterEndOfMonth = $daysAfterEndOfMonth;

        return $this;
    }

    
    public function getEndOfMonth(): ?bool
    {
        return $this->endOfMonth;
    }

   
    public function setEndOfMonth(?bool $endOfMonth): self
    {
        $this->endOfMonth = $endOfMonth;

        return $this;
    }
}