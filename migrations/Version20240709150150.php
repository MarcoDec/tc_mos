<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240709150150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la proportion et du type de conditionnement dans la table component_customer';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE component_customer ADD proportion DOUBLE PRECISION UNSIGNED DEFAULT \'100\' NOT NULL, ADD packaging_kind VARCHAR(30) DEFAULT NULL');
  }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE component_customer DROP proportion, DROP packaging_kind');
    }
}
