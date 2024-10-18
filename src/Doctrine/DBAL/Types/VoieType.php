<?php

namespace App\Doctrine\DBAL\Types;

use App\Doctrine\DBAL\Types\EnumType;

final class VoieType extends EnumType {
    final public const TYPE_FIL = 'Fil';
    final public const TYPE_BOUCHON = 'Bouchon';
    final public const TYPE_VIDE = 'Vide';
    final public const TYPES = [
        self::TYPE_FIL,
        self::TYPE_BOUCHON,
        self::TYPE_VIDE,
    ];

    public function getName(): string {
        return 'voie_type';
    }
}
