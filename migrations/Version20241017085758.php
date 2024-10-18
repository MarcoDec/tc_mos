<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017085758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accessoire ADD targeted_connecteur_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE accessoire ADD CONSTRAINT FK_8FD026A6C529F5C FOREIGN KEY (targeted_connecteur_id) REFERENCES connecteur (id)');
    }

    public function down(Schema $schema): void
    {
    
    }
}
