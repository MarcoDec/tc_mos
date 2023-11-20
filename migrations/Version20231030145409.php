<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030145409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance_sheet (id INT UNSIGNED AUTO_INCREMENT NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE balance_sheet ADD company_id INT UNSIGNED NOT NULL, ADD month INT NOT NULL, ADD year INT NOT NULL, ADD total_income_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_income_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_income_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD total_expense_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_expense_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_expense_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE balance_sheet ADD CONSTRAINT FK_194A20B0979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_194A20B0979B1AD6 ON balance_sheet (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet DROP FOREIGN KEY FK_194A20B0979B1AD6');
        $this->addSql('DROP INDEX IDX_194A20B0979B1AD6 ON balance_sheet');
        $this->addSql('ALTER TABLE balance_sheet DROP company_id, DROP month, DROP year, DROP total_income_code, DROP total_income_denominator, DROP total_income_value, DROP total_expense_code, DROP total_expense_denominator, DROP total_expense_value');
        $this->addSql('DROP TABLE balance_sheet');
    }
}
