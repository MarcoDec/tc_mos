<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926065016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clocking CHANGE creation_date creation_date DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE company RENAME INDEX fk_4fbf094f38248176 TO IDX_4FBF094F38248176');
        $this->addSql('ALTER TABLE nomenclature ADD sub_product_id INT UNSIGNED DEFAULT NULL, CHANGE component_id component_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE nomenclature ADD CONSTRAINT FK_799A3652694FF597 FOREIGN KEY (sub_product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_799A3652694FF597 ON nomenclature (sub_product_id)');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX fk_b8e90a2c8c03f15c TO IDX_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX fk_b8e90a2c44ac3583 TO IDX_B8E90A2C44AC3583');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clocking CHANGE creation_date creation_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE company RENAME INDEX idx_4fbf094f38248176 TO FK_4FBF094F38248176');
        $this->addSql('ALTER TABLE nomenclature DROP FOREIGN KEY FK_799A3652694FF597');
        $this->addSql('DROP INDEX IDX_799A3652694FF597 ON nomenclature');
        $this->addSql('ALTER TABLE nomenclature DROP sub_product_id, CHANGE component_id component_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX idx_b8e90a2c8c03f15c TO FK_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX idx_b8e90a2c44ac3583 TO FK_B8E90A2C44AC3583');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
    }
}
