<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219004639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout entité SinglePrinterMobileUnit pour la gestion des postes mobiles d\'impression d\'étiquette';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE single_printer_mobile_unit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, printer_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(255) DEFAULT NULL, mobile_unit_ip VARCHAR(255) DEFAULT NULL, INDEX IDX_EFA47FE746EC494A (printer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE single_printer_mobile_unit ADD CONSTRAINT FK_EFA47FE746EC494A FOREIGN KEY (printer_id) REFERENCES printer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE single_printer_mobile_unit DROP FOREIGN KEY FK_EFA47FE746EC494A');
        $this->addSql('DROP TABLE single_printer_mobile_unit');
    }
}
