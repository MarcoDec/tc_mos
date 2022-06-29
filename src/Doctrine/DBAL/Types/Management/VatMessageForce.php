<?php

namespace App\Doctrine\DBAL\Types\Management;

use App\Doctrine\DBAL\Types\EnumType;

final class VatMessageForce extends EnumType {
    final public const TYPE_FORCE_DEFAULT = 'TVA par défaut selon le pays du client';
    final public const TYPE_FORCE_WITH = 'Force AVEC TVA';
    final public const TYPE_FORCE_WITHOUT = 'Force SANS TVA';
    final public const TYPES = [self::TYPE_FORCE_DEFAULT, self::TYPE_FORCE_WITH, self::TYPE_FORCE_WITHOUT];

    public function getName(): string {
        return 'vat_message_force';
    }
}
