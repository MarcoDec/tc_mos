<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DBALType;

abstract class Type extends DBALType {
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool {
        return true;
    }
}
