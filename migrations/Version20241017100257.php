<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017100257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE voie ADD marquage VARCHAR(255) DEFAULT NULL, ADD num INT NOT NULL, CHANGE type type ENUM(\'Fil\', \'Bouchon\', \'Vide\') DEFAULT \'Vide\' NOT NULL COMMENT \'(DC2Type:voie_type)\'');
        $this->addSql('ALTER TABLE voie ADD CONSTRAINT FK_A57CE9784A8D987E FOREIGN KEY (connecteur_id) REFERENCES connecteur (id)');
    }

    public function down(Schema $schema): void
    {
    }
}
