<?php

namespace App\Entity\Embeddable\Hr\Employee;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Embeddable\Hr\Employee\EmployeeStateType;
use App\Entity\Embeddable\State as AbstractState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Embeddable]
class State extends AbstractState {
    final public const TRANSITIONS = [self::TR_SUPERVISE, self::TR_VALIDATE, self::TR_CLOSE];

    #[
        ApiProperty(description: 'état', openapiContext: ['enum' => EmployeeStateType::TYPES]),
        ORM\Column(type: 'employee_state', options: ['default' => 'warning']),
        Serializer\Groups(['read:state'])
    ]
    protected string $state = EmployeeStateType::TYPE_STATE_WARNING;
}