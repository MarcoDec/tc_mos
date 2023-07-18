<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230718081735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family DROP file_path');
        $this->addSql('ALTER TABLE customer ADD quality_portal_password VARCHAR(255) DEFAULT NULL, ADD quality_portal_url VARCHAR(255) DEFAULT NULL, ADD quality_portal_username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family ADD file_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer DROP quality_portal_password, DROP quality_portal_url, DROP quality_portal_username');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
    }
}
