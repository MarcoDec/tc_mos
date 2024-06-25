<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625080342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout grille de prix pour les produits.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplier_product (id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, incoterms_id INT UNSIGNED DEFAULT NULL, supplier_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, code VARCHAR(255) DEFAULT NULL, `index` VARCHAR(255) DEFAULT \'0\' NOT NULL, packaging_kind VARCHAR(30) DEFAULT NULL, proportion DOUBLE PRECISION UNSIGNED DEFAULT \'100\' NOT NULL, copper_weight_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, copper_weight_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, copper_weight_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, delivery_time_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, delivery_time_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, delivery_time_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, moq_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, moq_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, moq_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, packaging_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, packaging_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, packaging_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_522F70B24584665A (product_id), INDEX IDX_522F70B243D02C80 (incoterms_id), INDEX IDX_522F70B22ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_product_price (id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, ref VARCHAR(255) DEFAULT NULL, price_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, price_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_7949D9424584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B24584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B243D02C80 FOREIGN KEY (incoterms_id) REFERENCES incoterms (id)');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_product_price ADD CONSTRAINT FK_7949D9424584665A FOREIGN KEY (product_id) REFERENCES supplier_product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B24584665A');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B243D02C80');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B22ADD6D8C');
        $this->addSql('ALTER TABLE supplier_product_price DROP FOREIGN KEY FK_7949D9424584665A');
        $this->addSql('DROP TABLE supplier_product');
        $this->addSql('DROP TABLE supplier_product_price');
    }
}
