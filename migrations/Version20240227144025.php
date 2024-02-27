<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227144025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne preferred_warehouse_id dans la table employee et de la colonne old_id dans la table engine';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee ADD preferred_warehouse_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A177C0D2E6 FOREIGN KEY (preferred_warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A177C0D2E6 ON employee (preferred_warehouse_id)');
        $this->addSql('ALTER TABLE engine CHANGE old_id old_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A177C0D2E6');
        $this->addSql('DROP INDEX IDX_5D9F75A177C0D2E6 ON employee');
        $this->addSql('ALTER TABLE employee DROP preferred_warehouse_id');
        $this->addSql('ALTER TABLE engine CHANGE old_id old_id INT UNSIGNED NOT NULL');
    }
}
