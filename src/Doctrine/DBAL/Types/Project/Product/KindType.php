<?php

namespace App\Doctrine\DBAL\Types\Project\Product;

use App\Doctrine\DBAL\Types\EnumType;

final class KindType extends EnumType {
    final public const TYPE_EI = 'EI';
    final public const TYPE_PROTOTYPE = 'Prototype';
    final public const TYPE_SERIES = 'Série';
    final public const TYPE_SPARE = 'Pièce de rechange';
    final public const TYPES = [
        self::TYPE_EI,
        self::TYPE_PROTOTYPE,
        self::TYPE_SERIES,
        self::TYPE_SPARE
    ];

    public function getName(): string {
        return 'product_kind';
    }
}
