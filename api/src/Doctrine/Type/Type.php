<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DBALType;

abstract class Type extends DBALType {
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool {
        return true;
    }
}
