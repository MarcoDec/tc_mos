<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Management\Unit;

use App\Doctrine\Type\EnumType;
use App\Entity\Management\Unit\Area;
use App\Entity\Management\Unit\Boolean;
use App\Entity\Management\Unit\ElectricalResistance;
use App\Entity\Management\Unit\ElectricCurrent;
use App\Entity\Management\Unit\Length;
use App\Entity\Management\Unit\Mass;
use App\Entity\Management\Unit\Power;
use App\Entity\Management\Unit\Temperature;
use App\Entity\Management\Unit\Time;
use App\Entity\Management\Unit\Unit;
use App\Entity\Management\Unit\Unitary;
use App\Entity\Management\Unit\Voltage;
use App\Entity\Management\Unit\Volume;

class UnitType extends EnumType {
    /** @var string[] */
    final public const ENUM = [
        self::TYPE_AREA,
        self::TYPE_BOOLEAN,
        self::TYPE_ELECTRICAL_RESISTANCE,
        self::TYPE_ELECTRIC_CURRENT,
        self::TYPE_LENGTH,
        self::TYPE_MASS,
        self::TYPE_POWER,
        self::TYPE_TEMPERATURE,
        self::TYPE_TIME,
        self::TYPE_UNITARY,
        self::TYPE_VOLTAGE,
        self::TYPE_VOLUME
    ];

    /** @var 'unitary' */
    final public const TYPE_UNITARY = 'unitary';

    /** @var array<string, class-string<Unit>> */
    final public const TYPES = [
        self::TYPE_AREA => Area::class,
        self::TYPE_BOOLEAN => Boolean::class,
        self::TYPE_ELECTRICAL_RESISTANCE => ElectricalResistance::class,
        self::TYPE_ELECTRIC_CURRENT => ElectricCurrent::class,
        self::TYPE_LENGTH => Length::class,
        self::TYPE_MASS => Mass::class,
        self::TYPE_POWER => Power::class,
        self::TYPE_TEMPERATURE => Temperature::class,
        self::TYPE_TIME => Time::class,
        self::TYPE_UNITARY => Unitary::class,
        self::TYPE_VOLTAGE => Voltage::class,
        self::TYPE_VOLUME => Volume::class
    ];

    /** @var 'area' */
    private const TYPE_AREA = 'area';

    /** @var 'boolean' */
    private const TYPE_BOOLEAN = 'boolean';

    /** @var 'electric-current' */
    private const TYPE_ELECTRIC_CURRENT = 'electric-current';

    /** @var 'electrical-resistance' */
    private const TYPE_ELECTRICAL_RESISTANCE = 'electrical-resistance';

    /** @var 'length' */
    private const TYPE_LENGTH = 'length';

    /** @var 'mass' */
    private const TYPE_MASS = 'mass';

    /** @var 'power' */
    private const TYPE_POWER = 'power';

    /** @var 'temperature' */
    private const TYPE_TEMPERATURE = 'temperature';

    /** @var 'time' */
    private const TYPE_TIME = 'time';

    /** @var 'voltage' */
    private const TYPE_VOLTAGE = 'voltage';

    /** @var 'volume' */
    private const TYPE_VOLUME = 'volume';

    public function getName(): string {
        return 'unit';
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return self::ENUM;
    }
}
