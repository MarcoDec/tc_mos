<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\Migration;

final class Version20221116095433 extends Migration {
    /** @var string[] */
    private const TABLES = ['component_family', 'product_family', 'unit'];

    protected function defineQueries(): void {
        foreach (self::TABLES as $table) {
            $this->push("ALTER TABLE `$table` CHANGE `lft` `lft` INT NOT NULL, CHANGE `lvl` `lvl` INT NOT NULL, CHANGE `rgt` `rgt` INT NOT NULL");
        }
        $this->push(<<<'SQL'
SET @attribute_i = 0;
SELECT COUNT(*) INTO @attribute_count FROM `attribute` WHERE `attribut_id_family` IS NOT NULL;
WHILE @attribute_i < @attribute_count DO
    SET @attribute_sql = CONCAT(
        'SELECT `id`, `attribut_id_family` INTO @attribute_id, @attribute_families FROM `attribute` WHERE `attribut_id_family` IS NOT NULL LIMIT 1 OFFSET ',
        @attribute_i
    );
    PREPARE attribute_stmt FROM @attribute_sql;
    EXECUTE attribute_stmt;
    WHILE LENGTH(@attribute_families) > 0 DO
        SET @attribute_family_id = SUBSTR(@attribute_families, 1, 1);
        IF LENGTH(@attribute_families) > 2 THEN
            SET @attribute_families = SUBSTR(@attribute_families, 3);
        ELSE
            SET @attribute_families = '';
        END IF;
        SELECT `lft`, `rgt`, `root_id`
        INTO @attribute_family_lft, @attribute_family_rgt, @attribute_family_root
        FROM `component_family`
        WHERE `old_id` = @attribute_family_id
        AND `parent_id` IS NULL;
        INSERT INTO `attribute_family` (`attribute_id`, `family_id`)
        SELECT @attribute_id, `id`
        FROM `component_family`
        WHERE `lft` >= @attribute_family_lft
        AND `rgt` <= @attribute_family_rgt
        AND `root_id` = @attribute_family_root;
    END WHILE;
    SET @attribute_i = @attribute_i + 1;
END WHILE
SQL);
        $this->push('ALTER TABLE `attribute` DROP `attribut_id_family`');
        $this->push('ALTER TABLE `component_family` DROP `old_id`');
    }
}
