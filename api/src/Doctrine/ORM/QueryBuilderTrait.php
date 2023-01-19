<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

trait QueryBuilderTrait {
    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder {
        return (new QueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy)
            ->where("$alias.deleted = FALSE");
    }
}
