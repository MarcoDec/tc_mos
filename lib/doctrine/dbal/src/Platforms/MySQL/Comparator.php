<?php

namespace App\Doctrine\DBAL\Platforms\MySQL;

use Doctrine\DBAL\Platforms\MySQL\Comparator as MySQLComparator;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\TableDiff;

class Comparator extends MySQLComparator {
    public function doCompareSchemas(Schema $fromSchema, Schema $toSchema): SchemaDiff {
        $diff = parent::doCompareSchemas($fromSchema, $toSchema);
        $diff->changedTables = collect($diff->changedTables)
            ->filter(function (TableDiff $table): bool {
                $table->changedColumns = collect($table->changedColumns)
                    ->filter(function (ColumnDiff $column): bool {
                        if (
                            $column->column->hasCustomSchemaOption('collation')
                            && !empty($collation = $column->column->getCustomSchemaOption('collation'))
                        ) {
                            $column->column->setPlatformOption('collation', $collation);
                            $options = $column->column->getCustomSchemaOptions();
                            unset($options['collation']);
                            $column->column->setCustomSchemaOptions($options);
                        }
                        return !empty($column->changedProperties)
                            || (!empty($column->fromColumn) && $this->columnsEqual($column->fromColumn, $column->column));
                    })
                    ->all();
                return $table->newName !== false
                    || !empty($table->addedColumns)
                    || !empty($table->changedColumns)
                    || !empty($table->removedColumns)
                    || !empty($table->renamedColumns)
                    || !empty($table->addedIndexes)
                    || !empty($table->changedIndexes)
                    || !empty($table->removedIndexes)
                    || !empty($table->renamedIndexes)
                    || !empty($table->addedForeignKeys)
                    || !empty($table->changedForeignKeys)
                    || !empty($table->removedForeignKeys);
            })
            ->all();
        return $diff;
    }
}
