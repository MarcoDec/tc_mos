<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016135825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accessoire ADD deleted TINYINT(1) DEFAULT 0 NOT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE connecteur_id connecteur_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE connecteur ADD deleted TINYINT(1) DEFAULT 0 NOT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE voie ADD deleted TINYINT(1) DEFAULT 0 NOT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE connecteur_id connecteur_id INT UNSIGNED DEFAULT NULL, CHANGE couleur couleur_1 VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {}
}
