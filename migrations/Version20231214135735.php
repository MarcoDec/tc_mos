<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231214135735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Suppression champs logistique et produit dans label_template suite à revue avec Cyril';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE label_template DROP logistic_reference, DROP product_reference, DROP product_indice, DROP product_description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE label_template ADD logistic_reference VARCHAR(255) DEFAULT NULL, ADD product_reference VARCHAR(255) DEFAULT NULL, ADD product_indice VARCHAR(255) DEFAULT NULL, ADD product_description VARCHAR(255) DEFAULT NULL');
    }
}
