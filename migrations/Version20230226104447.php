<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226104447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, employee_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, category VARCHAR(255) NOT NULL, expiration_date DATE DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_E188696F8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter (id INT UNSIGNED AUTO_INCREMENT NOT NULL, link INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(255) NOT NULL, target VARCHAR(255) DEFAULT NULL, type ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL, value VARCHAR(255) NOT NULL, process VARCHAR(255) NOT NULL, INDEX IDX_2A97911036AC99F1 (link), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_attachment ADD CONSTRAINT FK_E188696F8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A97911036AC99F1 FOREIGN KEY (link) REFERENCES parameter (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee_attachment DROP FOREIGN KEY FK_E188696F8C03F15C');
        $this->addSql('ALTER TABLE parameter DROP FOREIGN KEY FK_2A97911036AC99F1');
        $this->addSql('DROP TABLE employee_attachment');
        $this->addSql('DROP TABLE parameter');
    }
}
