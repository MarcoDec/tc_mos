<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212103205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout classe LabelTemplate';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label_template (id INT UNSIGNED AUTO_INCREMENT NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, label_name VARCHAR(255) DEFAULT NULL, template_family VARCHAR(255) DEFAULT NULL, logistic_reference VARCHAR(255) DEFAULT NULL, product_reference VARCHAR(255) DEFAULT NULL, product_indice VARCHAR(255) DEFAULT NULL, product_description VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, customer_address_name VARCHAR(255) DEFAULT NULL, ship_from_address_name VARCHAR(255) DEFAULT NULL, customer_destination_point VARCHAR(255) DEFAULT NULL, vendor_number VARCHAR(255) DEFAULT NULL, label_kind VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE label_template');
    }
}
