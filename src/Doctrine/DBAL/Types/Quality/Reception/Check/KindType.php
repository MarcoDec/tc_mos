<?php

namespace App\Doctrine\DBAL\Types\Quality\Reception\Check;

use App\Doctrine\DBAL\Types\EnumType;

final class KindType extends EnumType {
    final public const TYPE_DIM = 'Dimensionnel';
    final public const TYPE_DOC = 'Documentaire';
    final public const TYPE_GON = 'GO/NOGO';
    final public const TYPE_QTE = 'Quantitatif';
    final public const TYPE_VIS = 'Visuel';
    final public const TYPES = [
        self::TYPE_DIM,
        self::TYPE_DOC,
        self::TYPE_GON,
        self::TYPE_QTE,
        self::TYPE_VIS
    ];

    public function getName(): string {
        return 'check_kind';
    }
}
