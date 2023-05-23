<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Production\Engine;

use App\Doctrine\Type\EnumType;
use App\Entity\Production\Engine\CounterPart\Group as CounterPartGroup;
use App\Entity\Production\Engine\Group;
use App\Entity\Production\Engine\Tool\Group as ToolGroup;
use App\Entity\Production\Engine\Workstation\Group as WorkstationGroup;

class EngineType extends EnumType {
    /** @var string[] */
    final public const ENUM = [self::TYPE_COUNTER_PART, self::TYPE_TOOL, self::TYPE_WORKSTATION];

    /** @var string */
    final public const TYPE_WORKSTATION = 'workstation';

    /** @var array<string, class-string<Group>> */
    final public const TYPES = [
        self::TYPE_COUNTER_PART => CounterPartGroup::class,
        self::TYPE_TOOL => ToolGroup::class,
        self::TYPE_WORKSTATION => WorkstationGroup::class
    ];

    /** @var string */
    private const TYPE_COUNTER_PART = 'counter-part';

    /** @var string */
    private const TYPE_TOOL = 'tool';

    public function getName(): string {
        return 'engine';
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return self::ENUM;
    }
}
