<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230905122659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edi (id INT UNSIGNED AUTO_INCREMENT NOT NULL, login_account_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, date DATETIME NOT NULL, input_json TEXT NOT NULL, file_path VARCHAR(255) NOT NULL, edi_type VARCHAR(255) NOT NULL, INDEX IDX_84A2A78656DCC703 (login_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE edi ADD CONSTRAINT FK_84A2A78656DCC703 FOREIGN KEY (login_account_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE edi ADD input_ref TEXT NOT NULL');
        $this->addSql('ALTER TABLE edi ADD edi_mode VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE edi CHANGE file_path file_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE edi DROP FOREIGN KEY FK_84A2A78656DCC703');
        $this->addSql('ALTER TABLE edi DROP input_ref');
        $this->addSql('ALTER TABLE edi DROP edi_mode');
        $this->addSql('ALTER TABLE edi CHANGE file_path file_path VARCHAR(255) NOT NULL');
        $this->addSql('DROP TABLE edi');
    }
}
