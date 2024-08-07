<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710081133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adaptation de la table product_customer pour la gestion des prix';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product_company DROP FOREIGN KEY IDX_9E6612FF4584665A');
        $this->addSql('ALTER TABLE product_company DROP FOREIGN KEY IDX_9E6612FF979B1AD6');
        $this->addSql('DROP TABLE product_company');
        $this->addSql('ALTER TABLE product_customer ADD administered_by_id INT UNSIGNED DEFAULT NULL, ADD incoterms_id INT UNSIGNED DEFAULT NULL, ADD code VARCHAR(255) DEFAULT NULL, ADD `index` VARCHAR(255) DEFAULT \'0\' NOT NULL, ADD packaging_kind VARCHAR(30) DEFAULT NULL, ADD proportion DOUBLE PRECISION UNSIGNED DEFAULT \'100\' NOT NULL, ADD copper_weight_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD copper_weight_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD copper_weight_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD delivery_time_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD delivery_time_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD delivery_time_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD moq_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD moq_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD moq_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD packaging_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD packaging_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD packaging_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE product_customer ADD CONSTRAINT FK_4A89E49E2753AB70 FOREIGN KEY (administered_by_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE product_customer ADD CONSTRAINT FK_4A89E49E43D02C80 FOREIGN KEY (incoterms_id) REFERENCES incoterms (id)');
        $this->addSql('CREATE INDEX IDX_4A89E49E2753AB70 ON product_customer (administered_by_id)');
        $this->addSql('CREATE INDEX IDX_4A89E49E43D02C80 ON product_customer (incoterms_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE product_company (product_id INT UNSIGNED NOT NULL, company_id INT UNSIGNED NOT NULL, INDEX IDX_9E6612FF979B1AD6 (company_id), INDEX IDX_9E6612FF4584665A (product_id), PRIMARY KEY(product_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_company ADD CONSTRAINT IDX_9E6612FF4584665A FOREIGN KEY (product_id) REFERENCES product_customer (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_company ADD CONSTRAINT IDX_9E6612FF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_customer DROP FOREIGN KEY FK_4A89E49E2753AB70');
        $this->addSql('ALTER TABLE product_customer DROP FOREIGN KEY FK_4A89E49E43D02C80');
        $this->addSql('DROP INDEX IDX_4A89E49E2753AB70 ON product_customer');
        $this->addSql('DROP INDEX IDX_4A89E49E43D02C80 ON product_customer');
        $this->addSql('ALTER TABLE product_customer DROP administered_by_id, DROP incoterms_id, DROP code, DROP `index`, DROP packaging_kind, DROP proportion, DROP copper_weight_code, DROP copper_weight_denominator, DROP copper_weight_value, DROP delivery_time_code, DROP delivery_time_denominator, DROP delivery_time_value, DROP moq_code, DROP moq_denominator, DROP moq_value, DROP packaging_code, DROP packaging_denominator, DROP packaging_value');
    }
}
