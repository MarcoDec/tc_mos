<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108143215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item CHANGE bill_date bill_date DATETIME DEFAULT NULL, CHANGE payment_date payment_date DATETIME DEFAULT NULL, CHANGE payment_ref payment_ref VARCHAR(255) DEFAULT NULL, CHANGE stakeholder stakeholder VARCHAR(255) DEFAULT NULL, CHANGE label label VARCHAR(255) DEFAULT NULL, CHANGE payment_method payment_method VARCHAR(255) DEFAULT NULL, CHANGE sub_category sub_category VARCHAR(255) DEFAULT NULL, CHANGE payment_category payment_category VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item CHANGE bill_date bill_date DATETIME NOT NULL, CHANGE payment_date payment_date DATETIME NOT NULL, CHANGE payment_ref payment_ref VARCHAR(255) NOT NULL, CHANGE stakeholder stakeholder VARCHAR(255) NOT NULL, CHANGE label label VARCHAR(255) NOT NULL, CHANGE payment_method payment_method VARCHAR(255) NOT NULL, CHANGE payment_category payment_category VARCHAR(255) NOT NULL, CHANGE sub_category sub_category VARCHAR(255) NOT NULL');
    }
}
