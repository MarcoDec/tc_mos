<?php

const NO_ITEM_GET_OPERATION = [
    'controller' => ApiPlatform\Core\Action\NotFoundAction::class,
    'openapi_context' => ['summary' => 'hidden'],
    'output' => false,
    'read' => false
];
