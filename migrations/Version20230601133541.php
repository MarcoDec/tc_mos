<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601133541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quality (id INT UNSIGNED AUTO_INCREMENT NOT NULL, component_id INT UNSIGNED DEFAULT NULL, supplier_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, comments VARCHAR(255) DEFAULT NULL, quality INT DEFAULT NULL, text VARCHAR(255) DEFAULT NULL, INDEX IDX_7CB20B10E2ABAFFF (component_id), INDEX IDX_7CB20B102ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quality ADD CONSTRAINT FK_7CB20B10E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE quality ADD CONSTRAINT FK_7CB20B102ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quality DROP FOREIGN KEY FK_7CB20B10E2ABAFFF');
        $this->addSql('ALTER TABLE quality DROP FOREIGN KEY FK_7CB20B102ADD6D8C');
        $this->addSql('DROP TABLE quality');
    }
}
