<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Collection;
use Doctrine\DBAL\Platforms\MySQL\Comparator as MySQLComparator;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\TableDiff;

class Comparator extends MySQLComparator {
    protected function doCompareSchemas(Schema $fromSchema, Schema $toSchema): SchemaDiff {
        $diff = parent::doCompareSchemas($fromSchema, $toSchema);
        $diff->changedTables = (new Collection($diff->changedTables))
            ->filter(function (TableDiff $table): bool {
                $table->changedColumns = (new Collection($table->changedColumns))
                    ->filter(function (ColumnDiff $column): bool {
                        if (
                            $column->column->hasCustomSchemaOption('collation')
                            && empty($collation = $column->column->getCustomSchemaOption('collation')) === false
                        ) {
                            $column->column->setPlatformOption('collation', $collation);
                            $options = $column->column->getCustomSchemaOptions();
                            unset($options['collation']);
                            $column->column->setCustomSchemaOptions($options);
                        }
                        return empty($column->changedProperties) === false
                            || (empty($column->fromColumn) === false && $this->columnsEqual($column->fromColumn, $column->column));
                    })
                    ->toArray();
                return $table->newName !== false
                    || empty($table->addedColumns) === false
                    || empty($table->changedColumns) === false
                    || empty($table->removedColumns) === false
                    || empty($table->renamedColumns) === false
                    || empty($table->addedIndexes) === false
                    || empty($table->changedIndexes) === false
                    || empty($table->removedIndexes) === false
                    || empty($table->renamedIndexes) === false
                    || empty($table->addedForeignKeys) === false
                    || empty($table->changedForeignKeys) === false
                    || empty($table->removedForeignKeys) === false;
            })
            ->toArray();
        return $diff;
    }
}
