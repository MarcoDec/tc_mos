<?php

namespace App\Doctrine\DBAL\Types\Management\Society;

use App\Doctrine\DBAL\Types\EnumType;

final class ContactType extends EnumType {
    public const TYPE_ACCOUNTING = 'comptabilité';
    public const TYPE_COSTING = 'chiffrage';
    public const TYPE_DIRECTION = 'direction';
    public const TYPE_ENGINEERING = 'ingénierie';
    public const TYPE_MANUFACTURING = 'fabrication';
    public const TYPE_PURCHASING = 'achat';
    public const TYPE_QUALITY = 'qualité';
    public const TYPE_SELLING = 'commercial';
    public const TYPE_SUPPLYING = 'approvisionnement';
    public const TYPES = [
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
        return 'contact_type';
    }
}
