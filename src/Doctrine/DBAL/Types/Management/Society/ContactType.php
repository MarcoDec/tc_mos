<?php

namespace App\Doctrine\DBAL\Types\Management\Society;

use App\Doctrine\DBAL\Types\EnumType;

final class ContactType extends EnumType {
    final public const TYPE_ACCOUNTING = 'comptabilité';
    final public const TYPE_COSTING = 'chiffrage';
    final public const TYPE_DIRECTION = 'direction';
    final public const TYPE_ENGINEERING = 'ingénierie';
    final public const TYPE_MANUFACTURING = 'fabrication';
    final public const TYPE_PURCHASING = 'achat';
    final public const TYPE_QUALITY = 'qualité';
    final public const TYPE_SELLING = 'commercial';
    final public const TYPE_SUPPLYING = 'approvisionnement';
    final public const TYPES = [
        self::TYPE_ACCOUNTING,
        self::TYPE_COSTING,
        self::TYPE_DIRECTION,
        self::TYPE_ENGINEERING,
        self::TYPE_MANUFACTURING,
        self::TYPE_PURCHASING,
        self::TYPE_QUALITY,
        self::TYPE_SELLING,
        self::TYPE_SUPPLYING
    ];

    public function getName(): string {
        return 'contact';
    }
}
