<?php

namespace App\Entity\Purchase\Component;

use App\Entity\Entity;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
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
        ApiProperty(description: 'fin', example: '2021-10-26T17:00:00.000Z'),
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $end = null;

    #[
        ApiProperty(description: 'fin pause', example: '2021-10-26T13:00:00.335Z'),
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $endBreak = null;

    #[
        ApiProperty(description: 'début', example: '2021-10-26T08:00:00.335Z'),
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $start = null;
    #[
        ApiProperty(description: 'début pause', example: '2021-10-26T12:00:00.335Z'),
        ORM\Column( type:"time", nullable: true),
        Serializer\Groups(['read:timeslot', 'write:timeslot'])

    ]
    private ?DateTimeInterface $startBreak = null;

    #[
        ApiProperty(description: 'nom', example: 'Mlika'),
        ORM\Column,
        Serializer\Groups(['read:timeslot', 'write:timeslot']),
        Assert\NotBlank
        ]
    private ?string $name;

  

 
    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

 
    public function setEnd(?DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

 
    public function getEndBreak(): ?DateTimeInterface
    {
        return $this->endBreak;
    }

   
    public function setEndBreak(?DateTimeInterface $endBreak): self
    {
        $this->endBreak = $endBreak;

        return $this;
    }


    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

   
    public function setStart(?DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

   
    public function getStartBreak(): ?DateTimeInterface
    {
        return $this->startBreak;
    }

   
    public function setStartBreak(?DateTimeInterface $startBreak): self
    {
        $this->startBreak = $startBreak;

        return $this;
    }

  
    public function getName():?string
    {
        return $this->name;
    }

  
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
