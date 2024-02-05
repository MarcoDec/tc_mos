<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your !
 */
final class Version20230227085908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your 
        $this->addSql('CREATE TABLE product_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, category VARCHAR(255) NOT NULL, expiration_date DATE DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_EA3886904584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_attachment ADD CONSTRAINT FK_EA3886904584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your 
        $this->addSql('ALTER TABLE product_attachment DROP FOREIGN KEY FK_EA3886904584665A');
        $this->addSql('DROP TABLE product_attachment');
    }
}
