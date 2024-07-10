<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la relation parent_machine_id sur la table engine';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE engine ADD parent_machine_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE engine ADD CONSTRAINT FK_E8A81A8DB1F4DF76 FOREIGN KEY (parent_machine_id) REFERENCES engine (id)');
        $this->addSql('CREATE INDEX IDX_E8A81A8DB1F4DF76 ON engine (parent_machine_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE engine DROP FOREIGN KEY FK_E8A81A8DB1F4DF76');
        $this->addSql('DROP INDEX IDX_E8A81A8DB1F4DF76 ON engine');
        $this->addSql('ALTER TABLE engine DROP parent_machine_id');
    }
}
