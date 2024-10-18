<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016134656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `accessoire` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT; ');
        $this->addSql('ALTER TABLE `voie` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT; ');    
    }

    public function down(Schema $schema): void
    {
    }
}
