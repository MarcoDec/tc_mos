<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221110092602 extends AbstractMigration {
    /** @var string[] */
    private const TABLES = ['component_family', 'product_family', 'unit'];

    public function up(Schema $schema): void {
        foreach (self::TABLES as $table) {
            $this->addSql("ALTER TABLE `$table` CHANGE `lft` `lft` INT NOT NULL, CHANGE `lvl` `lvl` INT NOT NULL, CHANGE `rgt` `rgt` INT NOT NULL");
        }
    }
}
