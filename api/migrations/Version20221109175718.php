<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221109175718 extends AbstractMigration {
    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
ALTER TABLE `family`
    CHANGE `lft` `lft` INT NOT NULL,
    CHANGE `lvl` `lvl` INT NOT NULL,
    CHANGE `rgt` `rgt` INT NOT NULL
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `unit`
    CHANGE `lft` `lft` INT NOT NULL,
    CHANGE `lvl` `lvl` INT NOT NULL,
    CHANGE `rgt` `rgt` INT NOT NULL
SQL);
    }
}
