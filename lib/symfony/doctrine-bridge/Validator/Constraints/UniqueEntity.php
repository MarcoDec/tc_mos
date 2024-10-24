<?php

namespace App\Symfony\Bridge\Doctrine\Validator\Constraints;

use Attribute;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as DoctrineUniqueEntity;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class UniqueEntity extends DoctrineUniqueEntity {
    /**
     * @param string|string[] $fields
     * @param string[]        $groups
     */
    public function __construct(array|string $fields, ?array $groups = null) {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $fields[] = 'deleted';
        parent::__construct(fields: $fields, ignoreNull: false, groups: $groups);
    }
}
