<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702132730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_customer ADD incoterms_id INT UNSIGNED DEFAULT NULL, ADD code VARCHAR(255) DEFAULT NULL, ADD `index` VARCHAR(255) DEFAULT \'0\' NOT NULL, ADD copper_weight_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD copper_weight_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD copper_weight_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD delivery_time_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD delivery_time_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD delivery_time_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD moq_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD moq_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD moq_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD packaging_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD packaging_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD packaging_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE component_customer ADD CONSTRAINT FK_C33B1AE443D02C80 FOREIGN KEY (incoterms_id) REFERENCES incoterms (id)');
        $this->addSql('CREATE INDEX IDX_C33B1AE443D02C80 ON component_customer (incoterms_id)');
        $this->addSql('ALTER TABLE supplier_component ADD administered_by_id INT UNSIGNED DEFAULT NULL, ADD kind ENUM(\'EI\', \'Prototype\', \'Série\', \'Pièce de rechange\') DEFAULT \'Série\' NOT NULL COMMENT \'(DC2Type:product_kind)\'');
        $this->addSql('ALTER TABLE supplier_component ADD CONSTRAINT FK_D3CC9B892753AB70 FOREIGN KEY (administered_by_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_D3CC9B892753AB70 ON supplier_component (administered_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_customer DROP FOREIGN KEY FK_C33B1AE443D02C80');
        $this->addSql('DROP INDEX IDX_C33B1AE443D02C80 ON component_customer');
        $this->addSql('ALTER TABLE component_customer DROP incoterms_id, DROP code, DROP `index`, DROP copper_weight_code, DROP copper_weight_denominator, DROP copper_weight_value, DROP delivery_time_code, DROP delivery_time_denominator, DROP delivery_time_value, DROP moq_code, DROP moq_denominator, DROP moq_value, DROP packaging_code, DROP packaging_denominator, DROP packaging_value');
        $this->addSql('ALTER TABLE supplier_component DROP FOREIGN KEY FK_D3CC9B892753AB70');
        $this->addSql('DROP INDEX IDX_D3CC9B892753AB70 ON supplier_component');
        $this->addSql('ALTER TABLE supplier_component DROP administered_by_id, DROP kind');
    }
}
