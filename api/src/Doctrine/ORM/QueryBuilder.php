<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

class QueryBuilder extends DoctrineQueryBuilder {
    public function addLeftJoin(string $join, string $alias): self {
        return $this
            ->addSelect($alias)
            ->leftJoin($join, $alias, Join::WITH, "$alias.deleted = FALSE");
    }
}
