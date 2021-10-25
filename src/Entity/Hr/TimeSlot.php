<?php

namespace App\Entity\Purchase\Component;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['end' => 'partial', 'endBreak' => 'partial', 'name' => 'partial', 'start' => 'partial', 'startBreak' => 'partial']),
    ApiResource(
        description: 'TimeSlot',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les TimeSlots',
                    'summary' => 'Récupère les TimeSlot',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un TimeSlot',
                    'summary' => 'Créer une famille de composant',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un TimeSlot',
                    'summary' => 'Supprime un TimeSlot',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un TimeSlot',
                    'summary' => 'Modifie un TimeSlot',
                ]
            ]
        ],
        shortName: 'TimeSlot',
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_HR_ADMIN.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:timeslot', 'write:name'],
            'openapi_definition_name' => 'TimeSlot-write'
        ],
        normalizationContext: [
            'groups' => ['read:timeslot', 'read:id', 'read:name'],
            'openapi_definition_name' => 'TimeSlot-read'
        ],
        paginationEnabled: false
    ),
    ORM\Entity(),
    ORM\Table(name: 'timeslot')
]
class TimeSlot extends Entity {
    #[
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $end = null;

    #[
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $endBreak = null;

    #[
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $start = null;
    #[
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $startBreak = null;

    #[
        ORM\Column,
        Serializer\Groups(['read:timeslot', 'write:timeslot']),
        Assert\NotBlank
        ]
    private ?string $name;

  

    
    public function getEnd()
    {
        return $this->end;
    }

   
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    
    public function getEndBreak()
    {
        return $this->endBreak;
    }

     
    public function setEndBreak($endBreak)
    {
        $this->endBreak = $endBreak;

        return $this;
    }

    
    public function getStart()
    {
        return $this->start;
    }

    
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    
    public function getStartBreak()
    {
        return $this->startBreak;
    }

    
    public function setStartBreak($startBreak)
    {
        $this->startBreak = $startBreak;

        return $this;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
