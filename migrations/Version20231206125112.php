<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206125112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout Entité LabelCarton';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label_carton (id INT UNSIGNED AUTO_INCREMENT NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, logistic_reference VARCHAR(255) DEFAULT NULL, product_reference VARCHAR(255) DEFAULT NULL, product_indice VARCHAR(255) DEFAULT NULL, product_description VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, customer_address_name VARCHAR(255) DEFAULT NULL, ship_from_address_name VARCHAR(255) DEFAULT NULL, customer_destination_point VARCHAR(255) DEFAULT NULL, batchnumber VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, net_weight VARCHAR(255) DEFAULT NULL, gross_weight VARCHAR(255) DEFAULT NULL, date_str VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, number_of_boxes VARCHAR(255) DEFAULT NULL, label_number VARCHAR(255) DEFAULT NULL, vendor_number VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, zpl TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE label_carton');
    }
}
