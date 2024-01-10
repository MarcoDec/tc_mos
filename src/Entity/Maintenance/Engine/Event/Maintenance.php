<?php

namespace App\Entity\Maintenance\Engine\Event;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\EventState;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Maintenance\Engine\Planning;
use App\Entity\Production\Engine\Event\Event;
use App\Filter\RelationFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Controller\Production\Engine\ItemEventEnginePlanningController;

#[
ApiResource(
   description: 'Événement de maintenance préventive sur un équipement',
   collectionOperations: [
      'get' => [
         'openapi_context' => [
            'description' => 'Récupère les événements de maintenance préventive',
            'summary' => 'Récupère les événements de maintenance préventive',
         ],
      ],
      'filtreEnginePlanning' => [
         'controller' => ItemEventEnginePlanningController::class,
         'method' => 'GET',
         'openapi_context' => [
               'description' => 'Filtrer par engine', 
               'parameters' => [[
                  'in' => 'path',
                 'name' => 'api',
                 'schema' => [
                     'type' => 'integer',
                  ]
               ]],
             'summary' => 'Filtrer par engine'
         ],
         'path' => '/engine-events/filtreEngine/{api}',
         'read' => false,
         'write' => false
      ]
      ],
   itemOperations: [
      'delete' => [
         'openapi_context' => [
            'description' => 'Supprime un événement de maintenance préventive',
            'summary' => 'Supprime un événement de maintenance préventive',
         ],
         'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_ADMIN.'\')'
      ],
      'get' => NO_ITEM_GET_OPERATION,
      'patch' => [
         'openapi_context' => [
            'description' => 'Modifie un événement de maintenance préventive',
            'summary' => 'Modifie un événement de maintenance préventive',
         ],
         'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')'
      ],
      'promote' => [
         'controller' => PlaceholderAction::class,
         'deserialize' => false,
         'method' => 'PATCH',
         'openapi_context' => [
            'description' => 'Transite l\'événement de maintenance préventive à son prochain statut de workflow',
            'parameters' => [
               [
                  'in' => 'path',
                  'name' => 'transition',
                  'required' => true,
                  'schema' => ['enum' => EventState::TRANSITIONS, 'type' => 'string']
               ],
               [
                  'in' => 'path',
                  'name' => 'workflow',
                  'required' => true,
                  'schema' => ['enum' => ['event'], 'type' => 'string']
               ]
            ],
            'requestBody' => null,
            'summary' => 'Transite le l\'événement à son prochain statut de workflow'
         ],
         'path' => '/engine-maintenance-events/{id}/promote/{workflow}/to/{transition}',
         'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_WRITER.'\')',
         'validate' => false
      ]
   ],
   shortName: 'EngineMaintenanceEvent',
   attributes: [
      'security' => 'is_granted(\''.Roles::ROLE_MAINTENANCE_READER.'\')'
   ],
   denormalizationContext: [
      'groups' => ['write:engine-maintenance-event'],
      'openapi_definition_name' => 'EngineMaintenanceEvent-write'
   ],
   normalizationContext: [
      'groups' => ['read:engine-maintenance-event', 'read:id', 'read:state'],
      'openapi_definition_name' => 'EngineMaintenanceEvent-read',
      'skip_null_values' => false
   ],
   paginationClientEnabled: true
),
   ApiFilter(filterClass: RelationFilter::class, properties: ['engine', 'engine.zone']),
   ORM\Entity]

class Maintenance extends Event {
    #[
        ApiProperty(description: 'Planifié par'),
        ORM\ManyToOne,
        Serializer\Groups(['read:engine-maintenance-event'])
    ]
    private ?Planning $plannedBy = null;

    public function __construct()
    {
       parent::__construct();
       $this->name .= ": Maintenance préventive";
    }

   final public function getPlannedBy(): ?Planning {
        return $this->plannedBy;
    }

    final public function setPlannedBy(?Planning $plannedBy): self {
        $this->plannedBy = $plannedBy;
        return $this;
    }
}
