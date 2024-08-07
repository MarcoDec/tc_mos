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
        return 'Ajout de la colonne old_id dans la table engine';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE engine CHANGE old_id old_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE engine CHANGE old_id old_id INT UNSIGNED NOT NULL');
    }
}
