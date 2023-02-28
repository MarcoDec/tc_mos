<?php

namespace App\Entity\Embeddable;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\EmployeeEngineStateType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class EmployeeEngineState extends State {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => EmployeeEngineStateType::TYPES]),
        ORM\Column(type: 'employee_engine_state', options: ['default' => 'warning']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = EmployeeEngineStateType::TYPE_STATE_WARNING;
}
