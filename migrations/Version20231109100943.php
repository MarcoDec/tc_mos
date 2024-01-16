<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109100943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'remplacement champs AbstractAttachment par FileEntity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item ADD file_path VARCHAR(255) NOT NULL, DROP category, DROP expiration_date, DROP url');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item ADD expiration_date DATE DEFAULT NULL, ADD url VARCHAR(255) NOT NULL, CHANGE file_path category VARCHAR(255) NOT NULL');
    }
}
