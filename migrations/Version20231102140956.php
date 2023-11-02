<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102140956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance_sheet_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, bill_date DATETIME NOT NULL, payment_date DATETIME NOT NULL, payment_ref VARCHAR(255) NOT NULL, stakeholder VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, payment_method VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, sub_category VARCHAR(255) NOT NULL, quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, unit_price_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, unit_price_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, unit_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, vat_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, vat_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, vat_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, amount_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, amount_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, amount_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE balance_sheet_item');
    }
}
