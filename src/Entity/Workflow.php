<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
#[
    ApiResource(
        description:'Permet la gestion des workflows',
        collectionOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/workflows',
                'controller' => 'App\Controller\Workflow\EmptyGetController',
                'read' => false,
                'output' => false,
            ],
            'apply' => [
                'method' => 'POST',
                'path' => '/workflows/apply',
                'security' => "is_granted('ROLE_USER')",
                'controller' => 'App\Controller\Workflow\ApplyWorkflowController',
                'normalization_context' => [
                    'groups' => ['workflow:apply']
                ],
                'denormalization_context' => [
                    'groups' => ['workflow:apply']
                ],
                'openapi_context' => [
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'workflowName' => [
                                            'type' => 'string'
                                        ],
                                        'transition' => [
                                            'type' => 'string'
                                        ],
                                        'iri' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'can' => [
                'method' => 'POST',
                'path' => '/workflows/can',
                'security' => "is_granted('ROLE_USER')",
                'controller' => 'App\Controller\Workflow\CanWorkflowController',
                'normalization_context' => [
                    'groups' => ['workflow:can']
                ],
                'openapi_context' => [
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'iri' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'get_history' => [
                'method' => 'POST',
                'path' => '/workflows/history',
                'security' => "is_granted('ROLE_USER')",
                'controller' => 'App\Controller\Workflow\GetHistoryController',
                'normalization_context' => [
                    'groups' => ['workflow:history']
                ],
                'openapi_context' => [
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'iri' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        itemOperations: [
            'get' =>  NO_ITEM_GET_OPERATION
        ]
    )
]
class Workflow
{
    #[ ApiProperty(readable: true, writable: true, identifier: true) ]
    private string $iri;
    #[ ApiProperty(readable: true, writable: true, identifier: false) ]
    private string $workflowName;
    #[ ApiProperty(readable: true, writable: false, identifier: false) ]
    private string $currentState;
    #[ ApiProperty(readable: true, writable: false, identifier: false) ]
    private array $possibleActions;
    #[ ApiProperty(readable: false, writable: true, identifier: false) ]
    private string $transition;
}