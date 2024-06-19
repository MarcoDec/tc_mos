<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604074347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_customer ADD kind ENUM(\'EI\', \'Prototype\', \'Série\', \'Pièce de rechange\') DEFAULT \'Série\' NOT NULL COMMENT \'(DC2Type:product_kind)\'');
        $this->addSql('ALTER TABLE product_customer ADD kind ENUM(\'EI\', \'Prototype\', \'Série\', \'Pièce de rechange\') DEFAULT \'Série\' NOT NULL COMMENT \'(DC2Type:product_kind)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_customer DROP kind');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product_customer DROP kind');
    }
}
