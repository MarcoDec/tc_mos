<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604070649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des tables component_customer et customer_component_price pour la gestion des grilles tarifaires des composants par client';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE component_customer (id INT UNSIGNED AUTO_INCREMENT NOT NULL, administered_by_id INT UNSIGNED DEFAULT NULL, customer_id INT UNSIGNED NOT NULL, component_id INT UNSIGNED NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_C33B1AE42753AB70 (administered_by_id), INDEX IDX_C33B1AE49395C3F3 (customer_id), INDEX IDX_C33B1AE4E2ABAFFF (component_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_component_price (id INT UNSIGNED AUTO_INCREMENT NOT NULL, component_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, price_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, price_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_7EE6D16DE2ABAFFF (component_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE component_customer ADD CONSTRAINT FK_C33B1AE42753AB70 FOREIGN KEY (administered_by_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE component_customer ADD CONSTRAINT FK_C33B1AE49395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE component_customer ADD CONSTRAINT FK_C33B1AE4E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE customer_component_price ADD CONSTRAINT FK_7EE6D16DE2ABAFFF FOREIGN KEY (component_id) REFERENCES component_customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_customer DROP FOREIGN KEY FK_C33B1AE42753AB70');
        $this->addSql('ALTER TABLE component_customer DROP FOREIGN KEY FK_C33B1AE49395C3F3');
        $this->addSql('ALTER TABLE component_customer DROP FOREIGN KEY FK_C33B1AE4E2ABAFFF');
        $this->addSql('ALTER TABLE customer_component_price DROP FOREIGN KEY FK_7EE6D16DE2ABAFFF');
        $this->addSql('DROP TABLE component_customer');
        $this->addSql('DROP TABLE customer_component_price');
    }
}
