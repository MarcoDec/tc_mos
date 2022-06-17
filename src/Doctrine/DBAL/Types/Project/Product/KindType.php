<?php

namespace App\Doctrine\DBAL\Types\Project\Product;

use App\Doctrine\DBAL\Types\EnumType;

final class KindType extends EnumType {
    public const TYPE_EI = 'EI';
    public const TYPE_PROTOTYPE = 'Prototype';
    public const TYPE_SERIES = 'Série';
    public const TYPE_SPARE = 'Pièce de rechange';
    public const TYPES = [
        self::TYPE_EI,
        self::TYPE_PROTOTYPE,
        self::TYPE_SERIES,
        self::TYPE_SPARE
    ];

    public function getName(): string {
        return 'product_kind';
    }
}
