<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226234223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE engine_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, engine_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, category VARCHAR(255) NOT NULL, expiration_date DATE DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_99A45E5AE78C9C0A (engine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE engine_attachment ADD CONSTRAINT FK_99A45E5AE78C9C0A FOREIGN KEY (engine_id) REFERENCES engine (id)');
        $this->addSql('ALTER TABLE parameter CHANGE type type ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE engine_attachment DROP FOREIGN KEY FK_99A45E5AE78C9C0A');
        $this->addSql('DROP TABLE engine_attachment');
        $this->addSql('ALTER TABLE parameter CHANGE type type VARCHAR(255) NOT NULL');
    }
}
